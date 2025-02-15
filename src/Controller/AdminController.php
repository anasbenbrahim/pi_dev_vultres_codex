<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Fermier;
use App\Entity\Fournisseur;
use App\Form\ClientForm;
use App\Form\FermierForm;
use App\Form\FournisseurForm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bundle\SecurityBundle\Security;
use App\Security\SecurityAuthenticator;
use Symfony\Component\HttpFoundation\Request;

use Doctrine\ORM\EntityManagerInterface;

use App\Service\PasswordGenerator;
use App\Repository\UserRepository;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

#[Route('/admin')]
final class AdminController extends AbstractController
{
    #[Route('/dashboard', name: 'app_admin')]
    public function index(): Response
    {
        
        return $this->render('dashboard/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/listfermier', name: 'app_fermier_index')]
    public function listfermier(EntityManagerInterface $entityManager, Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security,MailerInterface $mailer,UserRepository $userRepository, PasswordGenerator $passwordGenerator): Response
    {
        $fermiers = $entityManager
            ->getRepository(Fermier::class)
            ->findAll();

            $fermier = new Fermier();
            $fermierForm = $this->createForm(FermierForm::class, $fermier);
            $fermierForm->handleRequest($request);


            if ($fermierForm->isSubmitted() && $fermierForm->isValid()) {
                $existingUser = $userRepository->findOneBy(['email' => $fermier->getEmail()]);
                if ($existingUser) {
                    $this->addFlash('error', 'Un Fermier avec cet email existe déjà.');
                    return $this->redirectToRoute('app_fermier_index');
                }
                /** @var string $plainPassword */
                
        
                $fermier->setRoles(['ROLE_FERMIER']);

                $password = $passwordGenerator->generatePassword();

                $fermier->setPassword(
                    $userPasswordHasher->hashPassword(
                        $fermier,
                        $password // <-- Utilisez la variable $password déjà générée
                    )
                );
                
        
                $entityManager->persist($fermier);
                $entityManager->flush();

                $email = (new Email())
                ->from('anasbenbrahim491@gmail.com')
                ->to($fermier->getEmail())
                ->subject('Vos informations de connexion')
                ->html($this->renderView(
                    'email/email.html.twig',
                    [
                        'email' => $fermier->getEmail(),
                        'password' => $password, // <-- Utilisez $password ici
                    ]
                ));

                $mailer->send($email);

                $this->addFlash('success', 'Compte médecin créé avec succès. Un email a été envoyé avec les informations de connexion.');
                return $this->redirectToRoute('app_fermier_index');
              
            }

        return $this->render('fermier/index.html.twig', [
            'fermiers' => $fermiers,
            'fermierFormType' => $fermierForm->createView(),
        ]);
    }


    #[Route('/listclient',name: 'app_customer_index')]
    public function listClient(EntityManagerInterface $entityManager, Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, MailerInterface $mailer,UserRepository $userRepository, PasswordGenerator $passwordGenerator): Response
    {
        $clients = $entityManager
            ->getRepository(Client::class)
            ->findAll();


            $client = new Client();
            $clientForm = $this->createForm(ClientForm::class, $client);
            $clientForm->handleRequest($request);

            if ($clientForm->isSubmitted() && $clientForm->isValid()) {
                $existingUser = $userRepository->findOneBy(['email' => $client->getEmail()]);
                if ($existingUser) {
                    $this->addFlash('error', 'Un utilisateur avec cet email existe déjà.');
                    return $this->redirectToRoute('app_customer_index');
                }
                /** @var string $plainPassword */
                
        
                $client->setRoles(['ROLE_CLIENT']);

                $password = $passwordGenerator->generatePassword();

                $client->setPassword(
                    $userPasswordHasher->hashPassword(
                        $client,
                        $password // <-- Utilisez la variable $password déjà générée
                    )
                );
                
        
                $entityManager->persist($client);
                $entityManager->flush();

                $email = (new Email())
                ->from('anasbenbrahim491@gmail.com')
                ->to($client->getEmail())
                ->subject('Vos informations de connexion')
                ->html($this->renderView(
                    'email/email.html.twig',
                    [
                        'email' => $client->getEmail(),
                        'password' => $password, // <-- Utilisez $password ici
                    ]
                ));

                $mailer->send($email);

                $this->addFlash('success', 'Compte médecin créé avec succès. Un email a été envoyé avec les informations de connexion.');
                return $this->redirectToRoute('app_customer_index');
              
            }

        return $this->render('customer/index.html.twig', [
            'clients' => $clients,
            'clientFormType' => $clientForm->createView(),
        ]);
    }

    #[Route('/listfournisseur',name: 'app_fournisseur_index')]
    public function listfournisseur(EntityManagerInterface $entityManager, Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, MailerInterface $mailer,UserRepository $userRepository, PasswordGenerator $passwordGenerator): Response
    {
        $fournisseurs = $entityManager
            ->getRepository(Fournisseur::class)
            ->findAll();

            $fournisseur = new Fournisseur();
            $fournisseurForm = $this->createForm(FournisseurForm::class, $fournisseur);
            $fournisseurForm->handleRequest($request);

            if ($fournisseurForm->isSubmitted() && $fournisseurForm->isValid()) {
                $existingUser = $userRepository->findOneBy(['email' => $fournisseur->getEmail()]);
                if ($existingUser) {
                    $this->addFlash('error', 'Un utilisateur avec cet email existe déjà.');
                    return $this->redirectToRoute('app_fournisseur_index');
                }
                /** @var string $plainPassword */
                
        
                $fournisseur->setRoles(['ROLE_FOURNISSEUR']);

                $password = $passwordGenerator->generatePassword();

                $fournisseur->setPassword(
                    $userPasswordHasher->hashPassword(
                        $fournisseur,
                        $password // <-- Utilisez la variable $password déjà générée
                    )
                );
                
        
                $entityManager->persist($fournisseur);
                $entityManager->flush();

                $email = (new Email())
                ->from('anasbenbrahim491@gmail.com')
                ->to($fournisseur->getEmail())
                ->subject('Vos informations de connexion')
                ->html($this->renderView(
                    'email/email.html.twig',
                    [
                        'email' => $fournisseur->getEmail(),
                        'password' => $password, // <-- Utilisez $password ici
                    ]
                ));

                $mailer->send($email);

                $this->addFlash('success', 'Compte médecin créé avec succès. Un email a été envoyé avec les informations de connexion.');
                return $this->redirectToRoute('app_fournisseur_index');
              
            }

            

        return $this->render('fournisseur/index.html.twig', [
            'fournisseurs' => $fournisseurs,
            'fournisseurFormType' => $fournisseurForm->createView(),
        ]);
    }


    #[Route('/fermier/{id}/edit', name: 'fermier_edit')]
    public function edit(Request $request, Fermier $fermier, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FermierForm::class, $fermier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_fermier_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('fermier/edit.html.twig', [
            'fermier' => $fermier,
            'form' => $form,
        ]);
    }

    #[Route('/client/{id}/edit', name: 'customer_edit')]
    public function editClient(Request $request, Client $client, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ClientForm::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_customer_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('customer/edit.html.twig', [
            'client' => $client,
            'form' => $form,
        ]);
    }


    #[Route('/fournisseur/{id}/edit', name: 'fournisseur_edit')]
    public function editFournisseur(Request $request, Fournisseur $fournisseur, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FournisseurForm::class, $fournisseur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_fournisseur_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('fournisseur/edit.html.twig', [
            'fournisseur' => $fournisseur,
            'form' => $form,
        ]);
    }





    #[Route('/admin/employee', name: 'app_emp')]
    public function employee(): Response
    {
        return $this->render('employee/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }
}
