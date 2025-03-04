<?php
namespace App\Service;

use Postmark\PostmarkClient;
use Psr\Log\LoggerInterface;

class PostmarkService
{
    private $client;
    private $logger;

    public function __construct(string $postmarkApiToken, LoggerInterface $logger)
    {
        $this->client = new PostmarkClient($postmarkApiToken);
        $this->logger = $logger;
    }

    public function sendEmail(string $from, string $to, string $subject, string $htmlBody, string $qrCode = null): array

    {
        try {
            $response = $this->client->sendEmail(
                $from,
                $to,
                $subject,
                $htmlBody . ($qrCode ? "<img src='{$qrCode}' alt='QR Code'/>" : "")

            );
        } catch (\Exception $e) {
            $this->logger->error('Error sending email: ' . $e->getMessage());
            throw $e; // Rethrow the exception after logging
        }

        // Log the response from Postmark
        $this->logger->info('Postmark response: ' . json_encode($response));
        
        return $response;
    }
}
