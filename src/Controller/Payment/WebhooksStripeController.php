<?php

namespace App\Controller\Payment;

use App\Entity\Event\Event;
use App\Entity\Event\RegistrationEvent;
use App\Entity\Payment;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use UnexpectedValueException;

class WebhooksStripeController extends AbstractController
{
    #[Route('/webhooks', name: 'app_webhooks_stripe')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $session = $request->getSession()->get(name: 'details_inscription');
        $endpoint_secret = $this->getParameter(name: 'STRIPE_WEBHOOK_SECRET');
        $payload = @file_get_contents(filename: 'php://input');

        $header = 'Stripe-Signature';
        $signature = $request->headers->get($header);

        $sig_header = $signature;
        $event = null;

        try {
            $event = Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
        } catch (UnexpectedValueException $e) {
            // Payload invalide
            echo $e->getMessage();
            http_response_code(response_code: 400);
            exit();
        } catch (SignatureVerificationException $e) {
            // Signature Invalide
            echo $e->getMessage();
            http_response_code(response_code: 400);
            exit();
        }

        if ($event->type === 'checkout.session.completed') {
            $metadata = $event->data->object->metadata;
            // Création de fichier pour le debug uniquement
            // file_put_contents(filename: 'details_inscription', data: $metadata);
            // file_put_contents(filename: 'checkout.completed', data: $event);

            $registrationEventSlug = $metadata['evenement_slug'];
            $eventRegistered = $em->getRepository(entityName: Event::class)
                ->findOneBy(['slug' => $registrationEventSlug]);

            $registrationId = $metadata['inscription_id'];
            $registration = $em->getRepository(entityName: RegistrationEvent::class)
                ->findOneBy(['id' => $registrationId]);

            // Passer à true le champ Paid de la table RegistrationEvent
            $registration->setPaid(Paid: true);

            // Sauvegarder les informations de paiement en base de données
            $payment = new Payment();
            $payment->setEvent(event: $eventRegistered);
            $payment->setRegistrationEvent(registrationEvent: $registration);
            $payment->setStripeSessionId(stripeSessionId: $event->data->object->id);
            $payment->setStripePaymentIntentId(stripePaymentIntentId: $event->data->object->payment_intent);
            $payment->setStripePaymentIntentStatus(stripePaymentIntentStatus: $event->data->object->payment_status);
            $payment->setUnitPrice((int)$metadata['prix_unitaire']);
            $payment->setAmount(amount: $event->data->object->amount_total);
            $payment->setReservedPlaces(reservedPlaces: $registration->getChildren()->count());

            $em->persist($payment);
            $em->flush();
        }

        return new Response(content: Response::HTTP_OK);
    }
}
