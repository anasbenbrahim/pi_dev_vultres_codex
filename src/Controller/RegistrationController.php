<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Employee;
use App\Entity\Fermier;
use App\Entity\Fournisseur;
use App\Entity\Superadmin;
use App\Entity\User;
use App\Form\ClientType;
use App\Form\RegistrationFormType;
use App\Security\SecurityAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use App\Form\FermierType;
use App\Form\FournisseurType;

class RegistrationController extends AbstractController
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }
    #[Route('/register', name: 'app_register')]
public function register(
    Request $request,
    UserPasswordHasherInterface $userPasswordHasher,
    Security $security,
    EntityManagerInterface $entityManager
): Response {
    // Formulaire Fermier
    $fermier = new Fermier();
    $fermierForm = $this->createForm(FermierType::class, $fermier);
    $fermierForm->handleRequest($request);

    if ($fermierForm->isSubmitted() && $fermierForm->isValid()) {
        /** @var string $plainPassword */
        $plainPassword = $fermierForm->get('plainPassword')->getData();

        $fermier->setRoles(['ROLE_FERMIER']);
        $fermier->setPassword($userPasswordHasher->hashPassword($fermier, $plainPassword));

        $fermier->setConfirmationToken(bin2hex(random_bytes(32))); // Generate confirmation token
        $entityManager->persist($fermier);
        $this->sendConfirmationEmail($fermier); // Send confirmation email
        $entityManager->flush();

        $this->addFlash('success', 'Please check your email to confirm your registration.');
        return $this->redirectToRoute('app_login');
    }

    // Formulaire Fournisseur
    $fournisseur = new Fournisseur();
    $fournisseurForm = $this->createForm(FournisseurType::class, $fournisseur);
    $fournisseurForm->handleRequest($request);

    if ($fournisseurForm->isSubmitted() && $fournisseurForm->isValid()) {
        /** @var string $plainPassword */
        $plainPassword = $fournisseurForm->get('plainPassword')->getData();

        $fournisseur->setRoles(['ROLE_FOURNISSEUR']);
        $fournisseur->setPassword($userPasswordHasher->hashPassword($fournisseur, $plainPassword));

        $fournisseur->setConfirmationToken(bin2hex(random_bytes(32))); // Generate confirmation token
        $entityManager->persist($fournisseur);
        $this->sendConfirmationEmail($fournisseur); // Send confirmation email
        $entityManager->flush();

        $this->addFlash('success', 'Please check your email to confirm your registration.');
        return $this->redirectToRoute('app_login');
    }



    $client = new Client();
    

    $clientForm = $this->createForm(ClientType::class, $client, [
        'is_edit' => false, // L'utilisateur n'est pas connecté, donc on affiche le champ de mot de passe
    ]);



    $clientForm->handleRequest($request);

    if ($clientForm->isSubmitted() && $clientForm->isValid()) {
        /** @var string $plainPassword */
        $plainPassword = $clientForm->get('plainPassword')->getData();

        $client->setRoles(['ROLE_CLIENT']);
        $client->setPassword($userPasswordHasher->hashPassword($client, $plainPassword));

        $client->setConfirmationToken(bin2hex(random_bytes(32))); // Generate confirmation token
        $entityManager->persist($client);
        $this->sendConfirmationEmail($client); // Send confirmation email
        $entityManager->flush();

        $this->addFlash('success', 'Please check your email to confirm your registration.');
        return $this->redirectToRoute('app_login');
    }

    // Passer les formulaires à la vue Twig
    return $this->render('registration/register.html.twig', [
        'fermierType' => $fermierForm->createView(),
        'fournissuerType' => $fournisseurForm->createView(),
        'clientFormType' => $clientForm->createView(),
    ]);
}

    private function sendConfirmationEmail($user): void
    {
        $email = (new Email())
            ->from('noreply@example.com')
            ->to($user->getEmail())
            ->subject('Please Confirm Your Email')
            ->html($this->renderView('email/email1.html.twig', [
                'user' => $user,
                'token' => $user->getConfirmationToken(),
            ]));

        $this->mailer->send($email);
    }

    #[Route('/confirm-email/{token}', name: 'app_confirm_email')]
    public function confirmEmail(string $token, EntityManagerInterface $entityManager): Response
    {
        // Find user by confirmation token
        $user = $entityManager->getRepository(Fermier::class)->findOneBy(['confirmationToken' => $token])
            ?? $entityManager->getRepository(Client::class)->findOneBy(['confirmationToken' => $token])
            ?? $entityManager->getRepository(Fournisseur::class)->findOneBy(['confirmationToken' => $token]);

        if (!$user || $user->getConfirmationToken() !== $token) {
            throw $this->createNotFoundException('Invalid confirmation token or token has already been used');
        }

        // Clear the confirmation token
        $user->setConfirmationToken(null);
        $entityManager->flush();

        // Redirect to login with success message
        $this->addFlash('success', 'Your email has been confirmed! You can now log in.');
        return $this->redirectToRoute('app_login');
    }
}
