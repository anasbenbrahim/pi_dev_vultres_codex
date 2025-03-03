<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Equipements;
use App\Entity\Event;
use App\Entity\Fermier;
use App\Entity\Fournisseur;
use App\Entity\Publication;
use App\Entity\Reclamation;
use App\Entity\Superadmin;
use App\Enum\EventType;
use App\Enum\Status;
use App\Form\EventFormType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Form\ClientForm;
use App\Form\FermierForm;
use App\Form\FournisseurForm;
use App\Form\PublicationType;
use App\Form\SuperadminType;
use App\Repository\EquipementsRepository;
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
use Doctrine\Persistence\ManagerRegistry;
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
            $page = $request->query->getInt('page', 1);
            $limit = 5; // Number of products per page
            $total = count($fermiers);
            $fermiers = array_slice($fermiers, ($page - 1) * $limit, $limit);

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
                $token = bin2hex(random_bytes(32));
        
                $fermier->setConfirmationToken($token); // Store the confirmation token
                $entityManager->persist($fermier);
                
                $entityManager->flush();

                // Generate a token for email confirmation
                $email = (new Email())
                ->from('anasbenbrahim491@gmail.com')
                ->to($fermier->getEmail())
                ->subject('Vos informations de connexion')
                ->html($this->renderView(
                    'email/email.html.twig',
                    [
                        'user' => $fermier, // Pass the fermier object as user
                        'email' => $fermier->getEmail(),
                        'password' => $password, // <-- Utilisez $password ici
                        'token' => $token, // Pass the generated token
                    ]
                ));

                $mailer->send($email);

                $this->addFlash('success', 'Compte médecin créé avec succès. Un email a été envoyé avec les informations de connexion.');
                return $this->redirectToRoute('app_fermier_index');
              
            }

        return $this->render('fermier/index.html.twig', [
            'fermiers' => $fermiers,
            'fermierFormType' => $fermierForm->createView(),
            'current_page' => $page,
            'total_pages' => ceil($total / $limit),
        ]);
    }


    #[Route('/listclient',name: 'app_customer_index')]
    public function listClient(EntityManagerInterface $entityManager, Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, MailerInterface $mailer,UserRepository $userRepository, PasswordGenerator $passwordGenerator): Response
    {
        $clients = $entityManager
            ->getRepository(Client::class)
            ->findAll();

            $page = $request->query->getInt('page', 1);
            $limit = 5; // Number of products per page
            $total = count($clients);
            $clients = array_slice($clients, ($page - 1) * $limit,$limit);
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
                $token = bin2hex(random_bytes(32));
        
                $client->setConfirmationToken($token); // Store the confirmation token
                $entityManager->persist($client);
                
                $entityManager->flush();

                 // Generate a token for email confirmation
                $email = (new Email())
                ->from('anasbenbrahim491@gmail.com')
                ->to($client->getEmail())
                ->subject('Vos informations de connexion')
                ->html($this->renderView(
                    'email/email.html.twig',
                    [
                        'user' => $client, // Pass the client object as user
                        'email' => $client->getEmail(),
                        'password' => $password, // <-- Utilisez $password ici
                        'token' => $token, // Pass the generated token
                    ]
                ));

                $mailer->send($email);

                $this->addFlash('success', 'Compte médecin créé avec succès. Un email a été envoyé avec les informations de connexion.');
                return $this->redirectToRoute('app_customer_index');
              
            }

        return $this->render('customer/index.html.twig', [
            'clients' => $clients,
            'clientFormType' => $clientForm->createView(),
            'current_page' => $page,
            'total_pages' => ceil($total/$limit),
        ]);
    }

    #[Route('/listfournisseur',name: 'app_fournisseur_index')]
    public function listfournisseur(EntityManagerInterface $entityManager, Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, MailerInterface $mailer,UserRepository $userRepository, PasswordGenerator $passwordGenerator): Response
    {
        $fournisseurs = $entityManager
            ->getRepository(Fournisseur::class)
            ->findAll();
            $page = $request->query->getInt('page', 1);
            $limit = 5; // Number of products per page
            $total = count($fournisseurs);
            $fournisseurs = array_slice($fournisseurs, ($page - 1) * $limit,$limit);
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
                
                $token = bin2hex(random_bytes(32));
        
                $fournisseur->setConfirmationToken($token); 
                $entityManager->persist($fournisseur);
                $entityManager->flush();
                // Generate a token for email confirmation
                $email = (new Email())
                ->from('anasbenbrahim491@gmail.com')
                ->to($fournisseur->getEmail())
                ->subject('Vos informations de connexion')
                ->html($this->renderView(
                    'email/email.html.twig',
                    [
                        'user' => $fournisseur,
                        'email' => $fournisseur->getEmail(),
                        'password' => $password, // <-- Utilisez $password ici
                        'token' => $token,
                    ]
                ));

                $mailer->send($email);

                $this->addFlash('success', 'Compte médecin créé avec succès. Un email a été envoyé avec les informations de connexion.');
                return $this->redirectToRoute('app_fournisseur_index');
              
            }

            

        return $this->render('fournisseur/index.html.twig', [
            'current_page' => $page,
            'total_pages' => ceil($total/$limit),
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


    #[Route('/{id}/edit', name: 'superadmin_edit')]
    public function editSuperadmin(Request $request, UserPasswordHasherInterface $userPasswordHasher, Superadmin $superadmin, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SuperadminType::class, $superadmin);
        $form->handleRequest($request);

        $plainPassword = $form->get('password')->getData();

        
        $superadmin->setPassword($userPasswordHasher->hashPassword($superadmin, $plainPassword));

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_profile', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('superadmin/edit.html.twig', [
            'superadmin' => $superadmin,
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

    


    #[Route('/publication', name: 'publication_index_admin')]
public function indexPublication(EntityManagerInterface $entityManager): Response
{
    $publications = $entityManager->getRepository(Publication::class)->findAll();

    // Rendre le premier template
    $template1 = $this->renderView('publication/indexadmin.html.twig', [
        'publications' => $publications,
    ]);

    // Rendre le deuxième template
    $template2 = $this->renderView('client/index.html.twig', [
        'publications' => $publications,
    ]);

    // Combiner les deux templates dans une seule réponse
    $combinedContent = $template1 . $template2;

    return new Response($combinedContent);
}

    

    #[Route('/publication/{id}/delete', name: 'publication_delete')]
    public function delete(Publication $publication, EntityManagerInterface $entityManager): Response
    {
        foreach ($publication->getCommentaires() as $commentaire) {
            $entityManager->remove($commentaire);
        }

        $entityManager->remove($publication);
        $entityManager->flush();

        return $this->redirectToRoute('publication_index_admin');
    }



    #[Route('/reclamations', name: 'admin_reclamation_index')]
    public function adminIndex(EntityManagerInterface $entityManager): Response
    {
        $reclamations = $entityManager->getRepository(Reclamation::class)->findAll();
        $publications = $entityManager->getRepository(Publication::class)->findAll();
        return $this->render('reclamation/indexadmin.html.twig' , [
            'reclamations' => $reclamations,
            'publications' => $publications,
        ]);
    }

    #[Route('/reclamation/{id}/delete', name: 'admin_reclamation_delete')]
    public function adminDelete(int $id, EntityManagerInterface $entityManager): Response
    {
        $reclamation = $entityManager->getRepository(Reclamation::class)->find($id);

        if (!$reclamation) {
            throw $this->createNotFoundException('Reclamation not found');
        }
        $entityManager->remove($reclamation);
        $entityManager->flush();
        $this->addFlash('success', 'Reclamation deleted successfully');
        return $this->redirectToRoute('admin_reclamation_index');
    }

    #[Route('/reclamation/{id}/approve', name: 'admin_reclamation_approve')]
    public function adminApprove(Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        $reclamation->setStatus(Status::TERMINE);  
        $entityManager->flush();
        $this->addFlash('success', 'Reclamation approved successfully');
        return $this->redirectToRoute('admin_reclamation_index');
    }








    #[Route('/events', name: 'app_event_index', methods: ['GET'])]
    public function indexevent(EntityManagerInterface $entityManager): Response
    {
        $events = $entityManager->getRepository(Event::class)->findAll();

        return $this->render('event/index.html.twig', [
            'events' => $events,
            
        ]);
    }
    



    // 🔹 Ajouter un événement avec gestion d'image
    #[Route('/new', name: 'app_event_new', methods: ['GET', 'POST'])]
    public function newevent(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $event = new Event();
        $form = $this->createForm(EventFormType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion de l'upload d'image
            $imageFile = $form->get('photo')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();
                
                try {
                    // Définir le dossier d'upload
                    $uploadDir = $this->getParameter('kernel.project_dir') . '/public/uploads';
                    
                    // Déplacer l'image vers le dossier d'upload
                    $imageFile->move($uploadDir, $newFilename);
                    
                    // Enregistrer le chemin de l'image dans la base de données
                    $event->setPhoto('uploads/' . $newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors du téléchargement de l\'image.');
                }
            }

            $entityManager->persist($event);
            $entityManager->flush();

            $this->addFlash('success', 'Événement ajouté avec succès !');

            return $this->redirectToRoute('app_event_index');
        }

        return $this->render('event/new.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }

    // 🔹 Afficher un seul événement
    #[Route('/{id}', name: 'app_event_show', methods: ['GET'])]
    public function show(Event $event): Response
    {
        return $this->render('event/show.html.twig', [
            'event' => $event,
        ]);
    }

    // 🔹 Modifier un événement (avec gestion d'image)
    #[Route('/event/{id}/edit', name: 'app_event_edit', methods: ['GET', 'POST'])]
    public function editevent(Request $request, Event $event, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(EventFormType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('photo')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                try {
                    $uploadDir = $this->getParameter('kernel.project_dir') . '/public/uploads';
                    $imageFile->move($uploadDir, $newFilename);
                    $event->setPhoto('uploads/' . $newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors du téléchargement de l\'image.');
                }
            }

            $entityManager->flush();

            $this->addFlash('success', 'Événement modifié avec succès !');

            return $this->redirectToRoute('app_event_index');
        }

        return $this->render('event/edit.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }

    // 🔹 Supprimer un événement
    #[Route('/{id}/delete', name: 'app_event_delete', methods: ['POST'])]
    public function deleteevent(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $event->getId(), $request->request->get('_token'))) {
            $entityManager->remove($event);
            $entityManager->flush();

            $this->addFlash('success', 'Événement supprimé avec succès !');
        }

        return $this->redirectToRoute('app_event_index');
    }



    #[Route('/equipement/show_equipement_dashboard_admin' , 'show_equipement_dashboard_admin')]
    public function showequip(ManagerRegistry $doctrine,EquipementsRepository $repo){
        
        $repo=$doctrine->getRepository(Equipements::class);
        $list=$repo->findAll();
        return $this->render( '/equipements/show.html.twig', [
            "list" =>$list
        ]);
    }

    #[Route('/{id}', name: 'app_fournisseur_delete', methods: ['POST'])]
    public function deletefournisseur(Request $request, Fournisseur $fournisseur, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$fournisseur->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($fournisseur);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_fournisseur_index', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/fermier/{id}', name: 'app_fermier_delete', methods: ['POST'])]
    public function deletefermier(Request $request, Fermier $fermier, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$fermier->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($fermier);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_fermier_index', [], Response::HTTP_SEE_OTHER);
    }



    
   
    #[Route('/client/{id}/ban', name: 'client_ban')]
    #[Route('/fermier/{id}/ban', name: 'fermier_ban')]
    #[Route('/fournisseur/{id}/ban', name: 'fournisseur_ban')]
    public function banClient(Client $client, EntityManagerInterface $entityManager): Response
    {
        $client->setIsBanned(true);
        $entityManager->flush();

        $this->addFlash('success', 'Client banni avec succès.');
        return $this->redirectToRoute('app_customer_index');
    }

    #[Route('/client/{id}/unban', name: 'client_unban')]
    public function unbanClient(Client $client, EntityManagerInterface $entityManager): Response
    {
        $client->setIsBanned(false);
        $entityManager->flush();

        $this->addFlash('success', 'Client débanni avec succès.');
        return $this->redirectToRoute('app_customer_index');
    }

    #[Route('/fermier/{id}/ban', name: 'fermier_ban')]
    #[Route('/fournisseur/{id}/ban', name: 'fournisseur_ban')]
    public function banFermier(Fermier $fermier, EntityManagerInterface $entityManager): Response
    {
        $fermier->setIsBanned(true);
        $entityManager->flush();

        $this->addFlash('success', 'Fermier banni avec succès.');
        return $this->redirectToRoute('app_fermier_index');
    }

    #[Route('/fermier/{id}/unban', name: 'fermier_unban')]
    public function unbanFermier(Fermier $fermier, EntityManagerInterface $entityManager): Response
    {
        $fermier->setIsBanned(false);
        $entityManager->flush();

        $this->addFlash('success', 'Fermier débanni avec succès.');
        return $this->redirectToRoute('app_fermier_index');
    }

    #[Route('/fournisseur/{id}/ban', name: 'fournisseur_ban')]
    public function banFournisseur(Fournisseur $fournisseur, EntityManagerInterface $entityManager): Response
    {
        $fournisseur->setIsBanned(true);
        $entityManager->flush();

        $this->addFlash('success', 'Fournisseur banni avec succès.');
        return $this->redirectToRoute('app_fournisseur_index');
    }

    #[Route('/fournisseur/{id}/unban', name: 'fournisseur_unban')]
    public function unbanFournisseur(Fournisseur $fournisseur, EntityManagerInterface $entityManager): Response
    {
        $fournisseur->setIsBanned(false);
        $entityManager->flush();

        $this->addFlash('success', 'Fournisseur débanni avec succès.');
        return $this->redirectToRoute('app_fournisseur_index');
    }










}
