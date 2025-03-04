<?php

namespace App\Controller;
use App\Service\TwilioService; // Add this line
use App\Entity\Demande;
use App\Form\DemandeType;
use App\Repository\DemandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Offer;
use Symfony\Component\Form\Extension\Core\Type\DateType;
#[Route('/demande')]
final class DemandeController extends AbstractController
{
    #[Route('/show',name: 'app_demande_index', methods: ['GET'])]
    public function index(DemandeRepository $demandeRepository): Response
    {
        return $this->render('demande/index.html.twig', [
            'demandes' => $demandeRepository->findAll(),
        ]);
    }

    // src/Controller/DemandeController.php
    #[Route('/demande/add/{offerId}', name: 'app_demande_add')]
    public function add(Request $request, EntityManagerInterface $entityManager, TwilioService $twilioService, int $offerId): Response
    {
        // Fetch the offer by its ID
        $offer = $entityManager->getRepository(Offer::class)->find($offerId);

        if (!$offer) {
            throw $this->createNotFoundException('Offre non trouvée.');
        }

        // Create a new Demande entity
        $demande = new Demande();
        $demande->setOffer($offer); // Link the demande to the offer

        // Create the form
        $form = $this->createForm(DemandeType::class, $demande);

        // Handle form submission
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Save the demande to the database
            $entityManager->persist($demande);
            $entityManager->flush();

            // Send SMS confirmation
            $phoneNumber = $demande->getPhoneNumber();
            $message = 'Your demande has been successfully submitted.';
            $twilioService->sendSms($phoneNumber, $message);

            // Redirect to a success page or the offer details page
            return $this->redirectToRoute('app_offer_show_front', ['id' => $offerId]);
        }

        // Render the form
        return $this->render('demande/add.html.twig', [
            'form' => $form->createView(),
            'offer' => $offer,
        ]);
    }

    #[Route('/new', name: 'app_demande_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $demande = new Demande();
        $form = $this->createForm(DemandeType::class, $demande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($demande);
            $entityManager->flush();

            return $this->redirectToRoute('app_demande_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('demande/new.html.twig', [
            'demande' => $demande,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_demande_show', methods: ['GET'])]
    public function show(Demande $demande): Response
    {
        return $this->render('demande/show.html.twig', [
            'demande' => $demande,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_demande_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Demande $demande, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DemandeType::class, $demande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_demande_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('demande/edit.html.twig', [
            'demande' => $demande,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_demande_delete', methods: ['POST'])]
    public function delete(Request $request, Demande $demande, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$demande->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($demande);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_demande_index', [], Response::HTTP_SEE_OTHER);
    }
}
