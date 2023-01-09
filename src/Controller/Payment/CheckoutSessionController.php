<?php

declare(strict_types=1);

namespace App\Controller\Payment;

use App\Entity\Event\Event;
use App\Entity\Event\RegistrationEvent;
use App\Services\MailService;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CheckoutSessionController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    #[Route('/stripe/create-session', name: 'app_create_checkout_session', methods: ['POST' , 'GET'])]
    public function checkout(Request $request): Response
    {
        $STRIPE_KEY_SECRET = $this->getParameter(name: 'STRIPE_KEY_SECRET');
        Stripe::setApiKey($STRIPE_KEY_SECRET);
        // $stripe = new StripeClient($STRIPE_KEY_SECRET);

        $session = $request->getSession()->get(name: 'details_inscription');

        /** On récupère les données de la session pour les détails de l'acheteur */
        $customer_details = [
            'email' => $session['representant_legal_email'],
            'name' => $session['representant_legal'],
            'phone' => $session['representant_legal_telephone'],
        ];

        /** Créez un nouveau client Stripe en utilisant les détails du client */
        /*$customer = Customer::create(params: [
            'email' => $customer_details['email'] ,
            'name' => $customer_details['name'] ,
            'phone' => $customer_details['phone'] ,
        ]);*/

       //dd($session);

        $checkout_session = Session::create(params: [
            'customer_email' => $session['representant_legal_email'],
            'mode' => 'payment' ,
            'success_url' => $this->generateUrl(
                route: 'app_payment_stripe_success_payment',
                parameters: [],
                referenceType: UrlGeneratorInterface::ABSOLUTE_URL
            ) ,
            'cancel_url' => $this->generateUrl(
                route: 'app_payment_stripe_cancel_payment',
                parameters: [],
                referenceType: UrlGeneratorInterface::ABSOLUTE_URL
            ) ,
            'payment_method_types' => ['card'] ,
            'metadata' => [
                'stripe_session_id' => $session['stripe_session_id'] ,
                'payment_id' => $session['id'] . '_' . $session['date_creation_session'],
                'evenement' => $session['evenement_nom'],
                'evenement_slug' => $session['evenement_slug'],
                'inscription_id' => $session['inscription_id'],
                'places_reservees' => $session['places_reservees'],
                'prix_unitaire' => $session['evenement_prix'],
                'prix_total' => $session['total'] ,
                'parent_nom' => $session['representant_legal'],
                'parent_email' => $session['representant_legal_email'],
                'parent_telephone' => $session['representant_legal_telephone'],
                'date_de_creation' => $session['date_creation_session'],
            ] ,
            'line_items' => [[
                'quantity' => $session['places_reservees'] ,
                'price_data' => [
                    'currency' => 'EUR' ,
                    'unit_amount' => $session['evenement_prix'],
                    'product_data' => [
                        'name' => $session['evenement_nom'],
                    ] ,
                ] ,
            ]] ,
        ]);
        $checkout_sessionId = $checkout_session->id;
        $session['checkout_sessionId'] = $checkout_sessionId;

        return $this->redirect($checkout_session->url, status: 303);
    }

    /** Redirection vers la page de succès */
    #[Route('/inscription/success', name: 'app_payment_stripe_success_payment', methods: ['GET'])]
    public function successPayment(Request $request, MailService $mailService): Response
    {
        // Détruire la session details_inscription
        $this->addFlash(
            type: 'success',
            message: 'Votre paiement et votre inscription ont été effectués avec succès !'
        );

        // Set RegistrationEvent to paid if event.price == 0
        if ($request->getSession()->get(name: 'details_inscription')['total'] == 0) {
            $registrationEvent = $this->em->getRepository(RegistrationEvent::class)->find(
                $request->getSession()->get(name: 'details_inscription')['inscription_id']
            );
            $registrationEvent->setPaid(true);
            $this->em->flush();
        }

        $session = $request->getSession()->get(name: 'details_inscription');

        $mailService->sendEmail(
            from: $session['representant_legal_email'],
            subject: 'Confirmation d\'inscription à l\'événement ' . $session['evenement_nom'],
            htmlTemplate: 'emails/confirmation_inscription.html.twig',
            context: [
                'nom' => $session['representant_legal'],
                'evenement' => $session['evenement_nom'],
                'places_reservees' => $session['places_reservees'],
                'prix_total' => $session['total'] / 100,
                'date_de_creation' => $session['date_creation_session'],
            ]
        );


        $request->getSession()->remove(name: 'details_inscription');
        return $this->redirectToRoute(route: 'app_home');
    }

    /** Redirection vers la page d'annulation */
    #[Route('/inscription/cancel', name: 'app_payment_stripe_cancel_payment', methods: ['GET'])]
    public function cancelPayment(Request $request): Response
    {
        $this->addFlash(
            type: 'error',
            message: 'Une erreur s\'est produite lors de votre paiement. 
            Veuillez contacter l\'APE pour vérification'
        );
        $session = $request->getSession()->get(name: 'details_inscription');

        return $this->render(view: 'stripe/cancel.html.twig');
    }
}
