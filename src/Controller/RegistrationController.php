<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Employee;
use App\Entity\Fermier;
use App\Entity\Fournisseur;
use App\Entity\Superadmin;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\SecurityAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager): Response
    {
        $user = new Fermier();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            $user->setFarmName('farme1');
            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            return $security->login($user, SecurityAuthenticator::class, 'main');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
            'firstName' => $form->get('firstName')->createView(),
            'lastName' => $form->get('lastName')->createView(),
            'email' => $form->get('email')->createView(),
            'plainPassword' => $form->get('plainPassword')->createView(),
            'agreeTerms' => $form->get('agreeTerms')->createView(),
        ]);
    }

    #[Route('/register', name: 'app_register')]
    public function registerClient(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager): Response
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

            // do anything else you need here, like send an email

            return $security->login($user, SecurityAuthenticator::class, 'main');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
            'firstName' => $form->get('firstName')->createView(),
            'lastName' => $form->get('lastName')->createView(),
            'email' => $form->get('email')->createView(),
            'plainPassword' => $form->get('plainPassword')->createView(),
            'agreeTerms' => $form->get('agreeTerms')->createView(),
        ]);
    }

    #[Route('/register', name: 'app_register')]
    public function registerFournisseur(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager): Response
    {
        $user = new Fournisseur();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            
            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            return $security->login($user, SecurityAuthenticator::class, 'main');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
            'firstName' => $form->get('firstName')->createView(),
            'lastName' => $form->get('lastName')->createView(),
            'email' => $form->get('email')->createView(),
            'plainPassword' => $form->get('plainPassword')->createView(),
            'agreeTerms' => $form->get('agreeTerms')->createView(),
        ]);
    }

    #[Route('/register', name: 'app_register')]
    public function registerEmployee(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager): Response
    {
        $user = new Employee();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            
            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            return $security->login($user, SecurityAuthenticator::class, 'main');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
            'firstName' => $form->get('firstName')->createView(),
            'lastName' => $form->get('lastName')->createView(),
            'email' => $form->get('email')->createView(),
            'plainPassword' => $form->get('plainPassword')->createView(),
            'agreeTerms' => $form->get('agreeTerms')->createView(),
        ]);
    }

    #[Route('/register', name: 'app_register')]
    public function registerAdmin(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager): Response
    {
        $user = new Superadmin();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            
            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            return $security->login($user, SecurityAuthenticator::class, 'main');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
            'firstName' => $form->get('firstName')->createView(),
            'lastName' => $form->get('lastName')->createView(),
            'email' => $form->get('email')->createView(),
            'plainPassword' => $form->get('plainPassword')->createView(),
            'agreeTerms' => $form->get('agreeTerms')->createView(),
        ]);
    }

    
}
