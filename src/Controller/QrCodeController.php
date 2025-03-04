<?php

namespace App\Controller;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter; // Import PngWriter for saving QR codes
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel; // Correct import for ErrorCorrectionLevel

use Psr\Log\LoggerInterface; // Import LoggerInterface
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Service\PostmarkService;

class QrCodeController extends AbstractController
{
    private $postmarkService;
    private $logger; // Add logger property

    public function __construct(PostmarkService $postmarkService, LoggerInterface $logger) // Inject LoggerInterface
    {
        $this->postmarkService = $postmarkService;
        $this->logger = $logger; // Initialize logger
    }

    #[Route('/qr-code', name: 'app_qr_code')]
    public function index(Request $request): Response
    {
        // Retrieve data from the request
        $eventId = $request->request->get('event_id');
        $userName = $request->request->get('user_name');

        // Generate the QR code data
        $data = sprintf('User: %s, Event ID: %d', $userName, $eventId);

        // Generate the QR code
        $qrCode = new QrCode($data, new Encoding('UTF-8'), ErrorCorrectionLevel::Low, 300, 10);

        // Check if the public directory exists, if not create it
        if (!is_dir('public')) {
            mkdir('public', 0755, true);
        }

        // Save the QR code to a public directory
        $qrCodePath = 'public/qrcode.png';
        $writer = new PngWriter();
        $result = $writer->write($qrCode);
        
        try {
            $result->saveToFile($qrCodePath);
            $this->logger->info('QR Code saved at: ' . $qrCodePath);
        } catch (\Exception $e) {
            $this->logger->error('Failed to save QR Code: ' . $e->getMessage());
            return new Response('Failed to save QR Code.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        $writer = new PngWriter();
        $result = $writer->write($qrCode);
        $result->saveToFile($qrCodePath);

        // Log the QR code path
        $this->logger->info('QR Code saved at: ' . $qrCodePath);

        // Prepare email content
        $htmlBody = sprintf('Voici votre code QR généré pour l\'événement ID: %d. <br><img src="cid:qrcode.png" />', $eventId);

        // Log the recipient's email address
        $recipientEmail = $this->getUser() ? $this->getUser()->getEmail() : 'User not authenticated';
        $this->logger->info('Attempting to send email to: ' . $recipientEmail);

        // Check if the user is authenticated
        if (!$this->getUser()) {
            $this->logger->error('User is not authenticated. Cannot send email.');
            return new Response('User is not authenticated.', Response::HTTP_FORBIDDEN);
        }

        // Send the QR code via email using Postmark
        try {
            $this->postmarkService->sendEmail(
                'mohbenkhebbab004@gmail.com', // Change this to the intended sender email
                $this->getUser()->getEmail(), // Ensure this is the correct recipient email
                'Votre code QR', // Subject of the email
                $htmlBody,
                'http://127.0.0.1:8000/qrcode.png', // Update to full URL for QR code image
            );
        } catch (\Exception $e) {
            // Log the error
            $this->logger->error('Failed to send email: ' . $e->getMessage());
            return new Response('Failed to send email.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Render the success template with QR code image
        return $this->render('qrcode/success.html.twig', [
            'qrCodePath' => '/qrcode.png',
        ]);
    }
}
