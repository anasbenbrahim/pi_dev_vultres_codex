<?php

namespace App\Security;

use App\Entity\Client;
use App\Entity\Fermier;
use App\Entity\Fournisseur;
use App\Entity\Superadmin;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

class SecurityAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';
    
    private $entityManager;

    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->getPayload()->getString('email');
        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $email);

        $userBadge = new UserBadge($email, function($userIdentifier) {
            $user = $this->entityManager->getRepository(User::class)
                ->findOneBy(['email' => $userIdentifier]);

            if (!$user) {
                throw new CustomUserMessageAuthenticationException('User not found.');
            }

            if ($user->getConfirmationToken() !== null) {
                throw new CustomUserMessageAuthenticationException(
                    'Please confirm your email address before logging in.'
                );
            }

            if ($user instanceof Client || $user instanceof Fermier || $user instanceof Fournisseur) {
                if ($user->getIsBanned()) {
                    throw new CustomUserMessageAuthenticationException(
                        'Your account is banned and cannot log in.'
                    );
                }
            }

            return $user;
        });

        return new Passport(
            $userBadge,
            new PasswordCredentials($request->getPayload()->getString('password')),
            [
                new CsrfTokenBadge('authenticate', $request->getPayload()->getString('_csrf_token')),
                new RememberMeBadge(),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        /** @var User $user */
        $user = $token->getUser();

        $adminRoles = ['ROLE_SUPER_ADMIN', 'ROLE_EMPLOYEE'];

        foreach ($adminRoles as $role) {
            if (in_array($role, $user->getRoles(), true)) {
                return new RedirectResponse($this->urlGenerator->generate('app_admin'));
            }
        }

        if (in_array('ROLE_CLIENT', $user->getRoles(), true)) {
            return new RedirectResponse($this->urlGenerator->generate('app_client'));
        }

        if (in_array('ROLE_FERMIER', $user->getRoles(), true)) {
            return new RedirectResponse($this->urlGenerator->generate('app_fermier'));
        }

        if (in_array('ROLE_FOURNISSEUR', $user->getRoles(), true)) {
            return new RedirectResponse($this->urlGenerator->generate('app_fournisseur'));
        }

        // Default redirect
        return new RedirectResponse($this->urlGenerator->generate('app_home'));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
