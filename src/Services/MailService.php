<?php

declare(strict_types=1);

namespace App\Services;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

class MailService
{
    public function __construct(
        private MailerInterface $mailer,
    ) {
    }

    public function sendEmail(
        string $from,
        string $to,
        string $subject,
        string $htmlTemplate,
        array $context,
    ): void {
        $email = (new TemplatedEmail());
        $email->from($from);
        $email->to($to);
        $email->subject($subject);
        $email->htmlTemplate(template: $htmlTemplate);
        $email->context(context: $context);

        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            //throwException('Erreur survenue lors de l\'envoi du mail : ' . $e->getMessage());
        }
    }
}
