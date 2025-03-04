<?php

namespace App\Controller;

use App\Service\PostmarkService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

namespace App\Controller;

use App\Service\PostmarkService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PostmarkEmailHandler extends AbstractController

{
    private $postmarkService;

    public function __construct(PostmarkService $postmarkService) 

    {
        $this->postmarkService = $postmarkService;
    }

    /**
     * @Route("/send-email", name="send_email")
     */
    public function sendEmail(string $qrCode = null): Response

    {
        // Log the start of the email sending process
        $this->logger->info('Attempting to send email...');

        $from = 'mohbenkhebbab004@gmail.com';
        $to = 'mohbenkhebbab004@gmail.com';
        $subject = 'Hello from Symfony and Postmark!';
        $htmlBody = '<strong>Hello</strong> dear Postmark user.' . ($qrCode ? "<img src='{$qrCode}' alt='QR Code'/>" : "");


        try {
            $response = $this->postmarkService->sendEmail($from, $to, $subject, $htmlBody, $qrCode);

            // Log the successful response
            $this->logger->info('Email sent successfully.', ['MessageID' => $response['MessageID']]);
        } catch (\Exception $e) {
            // Log the error
            $this->logger->error('Failed to send email: ' . $e->getMessage());
            return new Response('Failed to send email.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }



        return new Response('Email sent! Message ID: ' . $response['MessageID']);
    }
}
