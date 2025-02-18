<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Fermier;
use App\Form\EventFormType;
use App\Form\FermierForm;
use App\Form\FermierType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bundle\SecurityBundle\Security;
use App\Security\SecurityAuthenticator;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;








#[Route('/fermier')]
final class FermierController extends AbstractController
{

    #[Route(name: 'app_fermier')]
    public function index(): Response
    {
        
        return $this->render('fermier/home.html.twig', [
            'controller_name' => 'FermierController',
        ]);
    }
    
    #[Route('/new', name: 'app_new_fermier')]
    public function registerClient(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager ): Response
    {
        $client = new Fermier();
        $clientForm = $this->createForm(FermierType::class, $client);
        $clientForm->handleRequest($request);

        if ($clientForm->isSubmitted() && $clientForm->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $clientForm->get('plainPassword')->getData();
    
            $client->setRoles(['ROLE_FERMIER']);
            $client->setPassword($userPasswordHasher->hashPassword($client, $plainPassword));
    
            $entityManager->persist($client);
            $entityManager->flush();
    
            return $security->login($client, SecurityAuthenticator::class, 'main');
        }

        return $this->render('fermier/index.html.twig', [
            'clientFormType' => $clientForm->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_fermier_show', methods: ['GET'])]
    public function show(Fermier $fermier): Response
    {
        return $this->render('fermier/show.html.twig', [
            'fermier' => $fermier,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_fermier_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Fermier $fermier, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FermierType::class, $fermier);
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



    #[Route('/fermier/events', name: 'app_event_indexfermier', methods: ['GET'])]
    public function indexeventfermier(EntityManagerInterface $entityManager): Response
    {
        $events = $entityManager->getRepository(Event::class)->findAll();

        return $this->render('event/index.html.twig', [
            'events' => $events,
            
        ]);
    }


    #[Route('/event/new', name: 'app_event_new', methods: ['GET', 'POST'])]
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

            return $this->redirectToRoute('app_event_indexfermier');
        }

        return $this->render('event/new.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/eventfermier/{id}/edit', name: 'app_event_edit', methods: ['GET', 'POST'])]
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

            return $this->redirectToRoute('app_event_indexfermier');
        }

        return $this->render('event/edit.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }


    #[Route('/eventfermier/{id}/delete', name: 'app_event_delete', methods: ['POST'])]
    public function deleteevent(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $event->getId(), $request->request->get('_token'))) {
            $entityManager->remove($event);
            $entityManager->flush();

            $this->addFlash('success', 'Événement supprimé avec succès !');
        }

        return $this->redirectToRoute('app_event_indexfermier');
    }
    

    #[Route('/eventfermier/{id}', name: 'app_event_show', methods: ['GET'])]
    public function showevent(Event $event): Response
    {
        return $this->render('event/show.html.twig', [
            'event' => $event,
        ]);
    }

}
