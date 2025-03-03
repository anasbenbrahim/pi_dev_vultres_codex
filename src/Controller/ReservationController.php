<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Form\ReservationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Endroid\QrCode\QrCode; // Import the QR Code library

use Symfony\Component\Security\Core\Security;

class ReservationController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    #[Route('/reservations', name: 'app_reservation_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $reservations = $entityManager->getRepository(Reservation::class)->findAll();

        return $this->render('reservation/index.html.twig', [
            'reservations' => $reservations,
        ]);
    }

    #[Route('/reservation/new', name: 'app_reservation_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reservation = new Reservation();
        $form = $this->createForm(ReservationFormType::class, $reservation);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->security->getUser();
            $reservation->setUser($user);
            $qrCode = new QrCode($reservation->getId()); // Generate QR code for the reservation ID
            $qrCodePath = __DIR__ . '/../../public/qr_codes/' . $reservation->getId() . '.png';
            $qrCode->writeFile($qrCodePath); // Save QR code to file

            // Check if the QR code file was created successfully
            if (!file_exists($qrCodePath)) {
                // Handle the error (e.g., log an error message or throw an exception)
                $this->addFlash('error', 'Failed to generate QR code.');
                return $this->redirectToRoute('app_reservation_new');
            }


            $entityManager->persist($reservation); 
            $entityManager->flush();

            return $this->redirectToRoute('app_reservation_success', [
                'qrCodePath' => '/qr_codes/' . $reservation->getId() . '.png' // Pass QR code path to the success page
            ]);
        }

        return $this->render('reservation/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
