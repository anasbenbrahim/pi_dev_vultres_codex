<?php

namespace App\Controller;

use App\Entity\Offer;
use App\Form\OfferType;
use App\Repository\OfferRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Psr\Log\LoggerInterface;
use App\Repository\DemandeRepository; // Add this line


#[Route('/offer')]
final class OfferController extends AbstractController
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    #[Route(name: 'app_offer_index', methods: ['GET'])]
    public function index(OfferRepository $offerRepository): Response
    {
        return $this->render('offer/index.html.twig', [
            'offers' => $offerRepository->findAll(),
        ]);
    }

    #[Route('/front', name: 'app_offer_front')]
    public function front(OfferRepository $offerRepository): Response
    {
        // Fetch all offers from the database
        $offers = $offerRepository->findAll();

        // Extract unique domains
        $domains = array_unique(array_map(fn($offer) => $offer->getDomain(), $offers));

        // Render the Twig template with the offers and domains
        return $this->render('offer/front.html.twig', [
            'offers' => $offers,
            'domains' => $domains,
        ]);
    }

    #[Route('/front/add', name: 'app_offer_add')]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Create a new Offer entity
        $offer = new Offer();

        // Create the form
        $form = $this->createForm(OfferType::class, $offer);

        // Handle form submission
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Save the offer to the database
            $entityManager->persist($offer);
            $entityManager->flush();

            // Redirect to the offer list page
            return $this->redirectToRoute('app_offer_front');
        }

        // Render the form
        return $this->render('offer/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    // src/Controller/OfferController.php
    #[Route('/api/offers/calendar', name: 'api_offers_calendar', methods: ['GET'])]
    public function getCalendarEvents(OfferRepository $offerRepository): Response
    {
        $offers = $offerRepository->findAll();
        $events = [];

        foreach ($offers as $offer) {
            $events[] = [
                'title' => $offer->getNom(),
                'start' => $offer->getDateOffer()->format('Y-m-d'),
                'url' => $this->generateUrl('app_offer_show', ['id' => $offer->getId()]),
            ];
        }

        return $this->render('offer/calendar.html.twig', [
            'events' => $events,
        ]);
    }

    #[Route('/stats', name: 'app_stats')]
    public function stats(OfferRepository $offerRepository, DemandeRepository $demandeRepository): Response
    {
        $totalOffers = $offerRepository->count([]);
        $totalDemandes = $demandeRepository->count([]);
        $offersByDomain = $offerRepository->countByDomain();


        $domainLabels = array_keys($offersByDomain);
        $domainData = array_values($offersByDomain);

        return $this->render('offer/stats.html.twig', [
            'totalOffers' => $totalOffers,
            'totalDemandes' => $totalDemandes,
            'offersByDomain' => $offersByDomain,
            'domainLabels' => $domainLabels,
            'domainData' => $domainData // Ensure this is an array like ['Domain A' => 10, 'Domain B' => 20]
        ]);
    }

    // src/Controller/OfferController.php
    #[Route('/front/{id}/show', name: 'app_offer_show_front')]
    public function showfront(Offer $offer): Response
    {
        // Render the offer details page
        return $this->render('offer/showfront.html.twig', [
            'offer' => $offer,
        ]);
    }

    #[Route('/new', name: 'app_offer_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $offer = new Offer();
        $form = $this->createForm(OfferType::class, $offer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Log form submission
            $this->logger->info('New offer submitted: ', ['offer' => $form->getData()]);

            $entityManager->persist($offer);
            $entityManager->flush();

            $this->addFlash('success', 'Offer created successfully.');

            return $this->redirectToRoute('app_offer_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('offer/new.html.twig', [
            'offer' => $offer,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_offer_show', methods: ['GET'])]
    public function show(Offer $offer): Response
    {
        return $this->render('offer/show.html.twig', [
            'offer' => $offer,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_offer_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Offer $offer, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(OfferType::class, $offer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->logger->info('Offer update submitted: ', ['offer' => $form->getData()]);

            $entityManager->flush();

            $this->addFlash('success', 'Offer updated successfully.');

            return $this->redirectToRoute('app_offer_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('offer/edit.html.twig', [
            'offer' => $offer,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_offer_delete', methods: ['POST'])]
    public function delete(Request $request, Offer $offer, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $offer->getId(), $request->request->get('_token'))) {
            $entityManager->remove($offer);
            $entityManager->flush();

            $this->addFlash('success', 'Offer deleted successfully.');
        }

        return $this->redirectToRoute('app_offer_index', [], Response::HTTP_SEE_OTHER);
    }
}
