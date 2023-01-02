<?php

declare(strict_types=1);

namespace App\Controller\Payment;

use App\Services\DataSaveSessionServices;
use Stripe\Checkout\Session;
use Stripe\Customer;
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
     */
    public function __construct(
        private DataSaveSessionServices $dataSaveSessionServices,
        private RequestStack $requestStack,
    ) {
    }

    #[Route('/stripe/create-session', name: 'app_create_checkout_session', methods: ['GET' , 'POST'])]
    public function index(): Response
    {
        // get the STRIPE_KEY_SECRET from the .env file
        $STRIPE_KEY_SECRET = $this->getParameter(name: 'STRIPE_KEY_SECRET');
        Stripe::setApiKey($STRIPE_KEY_SECRET);

        $stripeCustomer = new StripeClient($STRIPE_KEY_SECRET);

        $session = $this->dataSaveSessionServices;

        // 2. On crée une session de paiement
        $DOMAIN = 'https://127.0.0.1:8000/';

        // On récupère les données de la session pour les détails de l'acheteur
        $customer_details = [
            'email' => $session->getData(key: 'representant_legal_email'),
            'name' => $session->getData(key: 'representant_legal'),
            'phone' => $session->getData(key: 'representant_legal_telephone'),
        ];

        // Créez un nouveau client Stripe en utilisant les détails du client
        $customer = Customer::create([
            'email' => $customer_details['email'],
            'name' => $customer_details['name'],
            'phone' => $customer_details['phone'],
        ]);

        $checkout_session = Session::create(params: [
            'customer_email' => $session->getData(key: 'representant_legal_email') ,
            'mode' => 'payment' ,
            'success_url' => $DOMAIN . 'inscription/success/{CHECKOUT_SESSION_ID}' ,
            'cancel_url' => $DOMAIN . 'inscription/cancel/{CHECKOUT_SESSION_ID}' ,
            'line_items' => [ [
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
        $getSession = $this->dataSaveSessionServices->getDatas();

        header(header: "HTTP/1.1 303 See Other");
        header(header: "Location: " . $checkout_session->url);

        return $this->redirect($checkout_session->url);

        // 1. Si le paiement est success
            // RedirectToRoute app_payment_stripe_success_payment
        // 2. Si le paiement est cancel
            // RedirectToRoute app_payment_stripe_cancel_payment
        // 3. Si le paiement est error
            // RedirectToRoute app_payment_stripe_cancel_payment

        // GoTo payment Stripe page

        /*return $this->render(view: 'stripe/payment.html.twig', parameters: [
            'stripe_key' => $_ENV["STRIPE_KEY_PUBLIC"],
            'stripe_checkout_session' => $checkout_session,
            'event' => $eventRegistered,
            'reservedPlaces' => $reservedPlaces,
            'unitPrice' => $unitPrice,
            'registration' => $registration,
        ]);*/
    }


    // Create Success Payment URL and Cancel Payment URL with parameters
    #[Route(
        '/inscription/success/{CHECKOUT_SESSION_ID}',
        name: 'app_payment_stripe_success_payment',
        methods: ['GET' , 'POST']
    )
    ]
    public function successPayment(): Response
    {
        return $this->render(view: 'stripe/success.html.twig');
    }

    #[Route(
        '/inscription/cancel/{CHECKOUT_SESSION_ID}',
        name: 'app_payment_stripe_cancel_payment',
        methods: ['GET' , 'POST']
    )
    ]
    public function cancelPayment(): Response
    {
        return $this->render(view: 'stripe/cancel.html.twig');
    }
}
