<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class NotificationService
{
    private $mailer;
    private $adminEmail; // Store the admin email address here

    public function __construct(MailerInterface $mailer, string $adminEmail)
    {
        $this->mailer = $mailer;
        $this->adminEmail = $adminEmail;
    }

    public function sendReclamationNotification(string $reclamationMessage)
    {
        $email = (new Email())
            ->from('noreply@example.com')
            ->to($this->adminEmail)
            ->subject('Nouvelle réclamation soumise')
            ->text($reclamationMessage);

        $this->mailer->send($email);
    }
}
