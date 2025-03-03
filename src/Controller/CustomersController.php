<?php

namespace App\Controller;

use App\Entity\Client;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface; // Import the MailerInterface
use Symfony\Component\Mime\Email; // Import the Email class
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface; // Import the UserPasswordHasherInterface
use Symfony\Bundle\SecurityBundle\Security; // Correct import for Security
use App\Repository\UserRepository; // Import the UserRepository
use App\Form\RegistrationFormType; // Import the RegistrationFormType

final class CustomersController extends AbstractController
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    #[Route('/confirm-email/{token}', name: 'app_confirm_email')]
    public function confirmEmail(string $token, EntityManagerInterface $entityManager): Response
    {
        $client = $entityManager->getRepository(Client::class)->findOneBy(['confirmationToken' => $token]);

        if (!$client) {
            throw $this->createNotFoundException('Invalid token.');
        }

        // Mark the client as verified (you may need to add a verified property in the Client entity)
        $client->setConfirmationToken(null); // Clear the token
        // $client->setIsVerified(true); // Uncomment if you have a verified property
        $entityManager->flush();

        return $this->redirectToRoute('app_client', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/customers/register', name: 'app_customer_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        $user = new Client();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            $user->setAddress('address');
            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            $entityManager->persist($user);
            $entityManager->flush();

            // Generate a confirmation token
            $user->setConfirmationToken(bin2hex(random_bytes(32)));
            $entityManager->flush();

            // Send confirmation email
            $email = (new Email())
                ->from('anasbenbrahim491@gmail.com')
                ->to($user->getEmail())
                ->subject('Please confirm your email')
                ->html($this->renderView('email/email.html.twig', [
                    'token' => $user->getConfirmationToken(),
                    'user' => $user,
                ]));

            $this->mailer->send($email); // Use dependency injection for the mailer service

            return $this->redirectToRoute('app_client');
        }

        return $this->render('customers/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    public function registerClient(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
        $user = new Client();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            $user->setAddress('address');
            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            $entityManager->persist($user);
            $entityManager->flush();

            // Generate a confirmation token
            $user->setConfirmationToken(bin2hex(random_bytes(32)));
            $entityManager->flush();

            // Send confirmation email
            $email = (new Email())
                ->from('noreply@example.com')
                ->to($user->getEmail())
                ->subject('Please confirm your email')
                ->html($this->renderView('email/email.html.twig', [
                    'token' => $user->getConfirmationToken(),
                    'user' => $user,
                ]));

            $this->mailer->send($email); // Use dependency injection for the mailer service

            return $this->redirectToRoute('app_customers');
        }

        return $this->render('customers/index.html.twig', [ 
            'confirmationToken' => $user->getConfirmationToken(), // Add this line
            'registrationForm' => $form,
            'firstName' => $form->get('firstName')->createView(),
            'lastName' => $form->get('lastName')->createView(),
            'email' => $form->get('email')->createView(),
            'plainPassword' => $form->get('plainPassword')->createView(),
            'agreeTerms' => $form->get('agreeTerms')->createView(),
            'users' => $users,
        ]);
    }
}
