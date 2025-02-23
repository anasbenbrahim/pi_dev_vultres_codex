<?php

namespace App\Security;

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

            if ($user && $user->getConfirmationToken() !== null) {
                throw new CustomUserMessageAuthenticationException(
                    'Please confirm your email address before logging in.'
                );
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

        if(in_array('ROLE_CLIENT', $user->getRoles(), true)){
            return new RedirectResponse($this->urlGenerator->generate('app_client'));
        }
        if(in_array('ROLE_FERMIER', $user->getRoles(), true)){
            return new RedirectResponse($this->urlGenerator->generate('app_fermier'));
        }

        if(in_array('ROLE_FOURNISSEUR', $user->getRoles(), true)){
            return new RedirectResponse($this->urlGenerator->generate('app_fournisseur'));
        }

        

        // For example:
        return new RedirectResponse($this->urlGenerator->generate('app_home'));
        throw new \Exception('TODO: provide a valid redirect inside '.__FILE__);
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
