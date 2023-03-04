<?php

namespace App\Tests\Services;

use App\Services\MailService;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mailer\MailerInterface;

class MailServiceTest extends TestCase
{
    /** @var MailerInterface|MockObject */
    private MailerInterface|MockObject $mailerMock;

    protected function setUp(): void
    {
        $this->mailerMock = $this->createMock(originalClassName: MailerInterface::class);
    }

    /** @test */
    public function SendEmail(): void
    {
        // Crée une instance de la classe pour tester
        $myMailer = new MailService($this->mailerMock);

        // Définit les paramètres du mail
        $from = 'sender@example.com';
        $to = 'recipient@example.com';
        $subject = 'Test email';
        $htmlTemplate = 'email_template.html.twig';
        $context = ['name' => 'Papoel Test'];

        // Envoie l'e-mail
        $myMailer->sendEmail($from, $to, $subject, $htmlTemplate, $context);

        // Vérifie que l'e-mail a été envoyé avec succès
        $this->expectNotToPerformAssertions();
    }
    /** @test */
    public function SendEmailReturnErrorWhenBadEmailAddressIsGiven(): void
    {
        // Crée une instance de la classe pour tester
        $myMailer = new MailService($this->mailerMock);

        // Définit les paramètres du mail
        $from = 'sender@example.com';
        $to = 'invalid_email';
        $subject = 'Test email';
        $htmlTemplate = 'email_template.html.twig';
        $context = ['name' => 'Papoel Test'];

        // Vérifie que l'e-mail a échoué en raison d'une adresse e-mail invalide
        $this->expectException(InvalidArgumentException::class);

        // Envoie l'e-mail
        $myMailer->sendEmail($from, $to, $subject, $htmlTemplate, $context);
    }
    public function testSendEmailWithEmptyContext(): void
    {
        // Crée une instance de la classe pour tester
        $myMailer = new MailService($this->mailerMock);

        // Définit les paramètres du mail avec un contexte vide
        $from = 'sender@example.com';
        $to = 'recipient@example.com';
        $subject = 'Test email';
        $htmlTemplate = 'email_template.html.twig';
        $context = [];

        // Envoie l'e-mail
        $myMailer->sendEmail($from, $to, $subject, $htmlTemplate, $context);

        // Vérifie que l'e-mail a été envoyé avec succès
        $this->expectNotToPerformAssertions();
    }

}
