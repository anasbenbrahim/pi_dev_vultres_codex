<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\Reservation;
use App\Form\EventFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/event')]
class EventController extends AbstractController
{
    // 🔹 Afficher tous les événements
    #[Route('/', name: 'app_event_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $events = $entityManager->getRepository(Event::class)->findAll();
        $reservations = $entityManager->getRepository(Reservation::class)->findAll();

        return $this->render('event/index.html.twig', [
            'events' => $events,
            'reservations' => $reservations,
        ]);
    }

    #[Route('/test','test',methods: ['GET'])]
    public function test(EntityManagerInterface $entityManager): Response
    {
        $events = $entityManager->getRepository(Event::class)->findAll();
        return $this->render('event/showevent.html.twig',['events' => $events]);
    }

    #[Route('/detail/{id}','event_detail',methods: ['GET'])]
    public function detail(EntityManagerInterface $entityManager,$id): Response
    {
        $events = $entityManager->getRepository(Event::class)->find($id);
        return $this->render('event/detaille.html.twig',['event' => $events]);
    }

    // 🔹 Ajouter un événement avec gestion d'image
    #[Route('/new', name: 'app_event_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
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

            // Set latitude and longitude (remove duplicate)

            $entityManager->persist($event);
            $entityManager->flush();
            
            // Set latitude and longitude
            $event->setLatitude($form->get('latitude')->getData());
            $event->setLongitude($form->get('longitude')->getData());

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
    #[Route('/{id}/edit', name: 'app_event_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Event $event, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
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
    public function delete(Request $request, Event $event, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $event->getId(), $request->request->get('_token'))) {
            $entityManager->remove($event);
            $entityManager->flush();

            $this->addFlash('success', 'Événement supprimé avec succès !');
        }

        return $this->redirectToRoute('app_event_index');
    }

    #[Route('/reserve', name: 'app_reservation', methods: ['POST'])]
    public function reserve(Request $request, EntityManagerInterface $entityManager): Response
    {
        $username = $request->request->get('username');
        $eventId = $request->request->get('event_id');

        // Logic to save the reservation in the database
        // Example: $reservation = new Reservation();
        // Set properties and persist...

        $entityManager->flush();

        return $this->redirectToRoute('app_event_index'); // Redirect to the index page
    }
}
