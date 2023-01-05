<?php

namespace App\Controller\Payment;

use App\Entity\Event\Event;
use App\Entity\Event\RegistrationEvent;
use App\Entity\Payment;
use App\Services\DataSaveSessionServices;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Stripe;
use Stripe\Webhook;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use UnexpectedValueException;

class WebhooksStripeController extends AbstractController
{
    public function __construct(
        private DataSaveSessionServices $dataSaveSessionServices,
        private RequestStack $requestStack,
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route('/webhooks', name: 'app_webhooks_stripe')]
    public function index()
    {
        $STRIPE_KEY_SECRET = $this->getParameter(name: 'STRIPE_KEY_SECRET');
        Stripe::setApiKey($STRIPE_KEY_SECRET);
        $endpoint_secret = 'whsec_bcc6ea4621e7b8f04a9bb5c218e05280ccdc950616587709936411f74a926fa1';
        /**
         * Les webhooks de Stripe sont des requêtes POST avec un corps JSON. Le JSON brut peut généralement être lu à
         * partir de stdin, mais cela peut varier en fonction de la configuration du serveur.
         *
         * Les données du webhook ne seront pas disponibles dans la superglobale $_POST car les demandes de webhook de
         * Stripe ne sont pas envoyées au format codé par formulaire.
         */

        $payload = @file_get_contents(filename: 'php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];

        /** Instancier un Paiement pour Sauvegarder en base de données **/
        $payment = new Payment();

        $event = null;

        try {
            $event = Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
        } catch (UnexpectedValueException $e) {
            // Payload invalide
            http_response_code(response_code: 400);
            exit();
        } catch (SignatureVerificationException $e) {
            // Signature Invalide
            http_response_code(response_code: 400);
            exit();
        }

        if ($event->type === 'checkout.session.completed') {
            /** Récupérer l'événement auquel l'utilisateur s'inscrit */
            $session = $this->dataSaveSessionServices;
            $registrationEventName = $session->getData(key: 'inscription_event_name');

            $event = $this->entityManager
                ->getRepository(Event::class)
                ->findOneBy(['slug' => $registrationEventName]);

            /** Récupérer l'inscription de l'utilisateur */
            $registrationId = $session->getData(key: 'registration_id');
            $registration = $this->entityManager
                ->getRepository(RegistrationEvent::class)
                ->findOneBy(['id' => $registrationId]);

            // Passer à true le champ Paid de la table RegistrationEvent
            $registration->setPaid(Paid: true);

            // Sauvegarder les informations de paiement en base de données
            $payment = new Payment();
            $payment->setEvent(event: $event);
            $payment->setRegistrationEvent(registrationEvent: $registration);
            $payment->setStripeSessionId(stripeSessionId: $event->getData(key: 'checkout_session_id'));
            $payment->setStripePaymentIntentId(stripePaymentIntentId: $event->getData(key: 'payment_intent'));
            $payment->setStripePaymentIntentStatus(stripePaymentIntentStatus: $event->getData(key: 'payment_status'));

            $this->entityManager->persist($payment);
            $this->entityManager->flush();
        }

        http_response_code(response_code: 200);
    }
}
