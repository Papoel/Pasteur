<?php

declare(strict_types=1);

namespace App\Controller\Payment;

use App\Entity\Event\Event;
use App\Entity\Event\RegistrationEvent;
use App\Entity\Payment;
use App\Services\DataSaveSessionServices;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Stripe\Customer;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;
use Stripe\StripeClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CheckoutSessionController extends AbstractController
{
    /**
     * @param DataSaveSessionServices $dataSaveSessionServices
     * @param RequestStack            $requestStack
     * @param EntityManagerInterface  $entityManager
     */
    public function __construct(
        private DataSaveSessionServices $dataSaveSessionServices,
        private RequestStack $requestStack,
        private EntityManagerInterface $entityManager
    ) {
    }

    /**
     * @throws ApiErrorException
     */
    #[Route('/stripe/create-session', name: 'app_create_checkout_session', methods: ['POST' , 'GET'])]
    public function index(): Response
    {
        $STRIPE_KEY_SECRET = $this->getParameter(name: 'STRIPE_KEY_SECRET');
        Stripe::setApiKey($STRIPE_KEY_SECRET);
        $stripe = new StripeClient($STRIPE_KEY_SECRET);

        $session = $this->dataSaveSessionServices;
        // 2. On crée une session de paiement
        $DOMAIN = 'https://127.0.0.1:8000/';

        /** On récupère les données de la session pour les détails de l'acheteur */
        $customer_details = [
            'email' => $session->getData(key: 'representant_legal_email') ,
            'name' => $session->getData(key: 'representant_legal') ,
            'phone' => $session->getData(key: 'representant_legal_telephone') ,
        ];

        /** Créez un nouveau client Stripe en utilisant les détails du client */
        $customer = Customer::create(params: [
            'email' => $customer_details['email'] ,
            'name' => $customer_details['name'] ,
            'phone' => $customer_details['phone'] ,
        ]);

        $checkout_session = Session::create(params: [
            'customer_email' => $session->getData(key: 'representant_legal_email') ,
            'mode' => 'payment' ,
            'success_url' => $DOMAIN . 'inscription/success/{CHECKOUT_SESSION_ID}' ,
            'cancel_url' => $DOMAIN . 'inscription/cancel/{CHECKOUT_SESSION_ID}' ,
            'payment_method_types' => ['card'] ,
            'metadata' => [
                'payment_id' => $session->getData(key: 'created_at')->format('Ymd')
                    . '-' .
                    $session->getData(key: 'inscription_event_name')
                    . '-' .
                    uniqid(prefix: 'paiement-', more_entropy: false) ,
                'evenement' => $session->getData(key: 'inscription_event_name') ,
                'inscription_id' => $session->getData(key: 'registration_id') ,
                'places_reservees' => $session->getData(key: 'reserved_places') ,
                'prix_unitaire' => $session->getData(key: 'unit_price') ,
                'prix_total' => $session->getData(key: 'total_price') ,
                'parent_nom' => $session->getData(key: 'representant_legal') ,
                'parent_email' => $session->getData(key: 'representant_legal_email') ,
                'parent_telephone' => $session->getData(key: 'representant_legal_telephone') ,
                'date_de_creation' => $session->getData(key: 'created_at')->format('d-m-Y') ,
                'evenement_debut' => $session->getData(key: 'event_start')->format('d-m-Y' . ' à ' . 'H:i') ,
            ] ,
            'line_items' => [[
                'quantity' => $session->getData(key: 'reserved_places') ,
                'price_data' => [
                    'currency' => 'EUR' ,
                    'unit_amount' => $session->getData(key: 'unit_price') ,
                    'product_data' => [
                        'name' => $session->getData(key: 'inscription_event_name') ,
                    ] ,
                ] ,
            ]] ,
        ]);

        $checkout_session_id = $checkout_session->id;


        /** Récupérer l'événement auquel l'utilisateur s'inscrit */
        /*$registrationEventName = $session->getData(key: 'inscription_event_name');
        $event = $this->entityManager
            ->getRepository(Event::class)
            ->findOneBy(['slug' => $registrationEventName]);*/

        /** Récupérer l'inscription de l'utilisateur */
        /*$registrationId = $session->getData(key: 'registration_id');
        $registration = $this->entityManager
            ->getRepository(RegistrationEvent::class)
            ->findOneBy(['id' => $registrationId]);*/

        /** enregistrer dans la table "paiement" si le paiement est réussis */
        /*if ($checkout_session->payment_intent === 'succeeded') {
            $payment = new Payment();
            $payment->setEvent($event);
            $payment->setRegistrationEvent($registration);
            $payment->setStripeSessionId($checkout_session_id);
            $payment->setStripePaymentIntentId($checkout_session->payment_intent);
            $payment->setStripePaymentIntentStatus($checkout_session->payment_status);

            $this->entityManager->persist($payment);
            $this->entityManager->flush();
        }*/

        return $this->redirect($checkout_session->url);
    }

    /** Redirection vers la page de succès */
    #[Route('/inscription/success/{CHECKOUT_SESSION_ID}', name: 'app_payment_stripe_success_payment', methods: ['GET'])]
    public function successPayment(): Response
    {
        return $this->render(view: 'stripe/success.html.twig');
    }

    /** Redirection vers la page d'annulation */
    #[Route('/inscription/cancel/{CHECKOUT_SESSION_ID}', name: 'app_payment_stripe_cancel_payment', methods: ['GET'])]
    public function cancelPayment(): Response
    {
        return $this->render(view: 'stripe/cancel.html.twig');
    }
}
