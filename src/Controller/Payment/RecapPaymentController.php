<?php

namespace App\Controller\Payment;

use App\Entity\Event\Event;
use App\Entity\Event\RegistrationEvent;
use App\Repository\Event\EventRepository;
use App\Repository\Event\RegistrationEventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Services\MailService;

class RecapPaymentController extends AbstractController
{
    #[Route('/inscription/{event}/{id}', name: 'app_session_payment', methods: ['GET' , 'POST'])]
    public function validate(
        Request $request,
        RegistrationEvent $registrationEvent,
        EventRepository $eventRepository,
        RegistrationEventRepository $registrationEventRepository,
    ): Response {
        // Forbiden the access road if the $session['registration_details'] is empty
        $session = $request->getSession()->get(name: 'details_inscription', default: []);
        if (empty($session)) {
            return $this->redirectToRoute(route: 'app_home');
        }

        // On récupère l'événement
        $eventRegistered = $eventRepository->findOneBy(['slug' => $registrationEvent->getEvent()->getSlug()]);
        // On récupère le nombre de places réservées
        $reservedPlaces = count($registrationEvent->getChildren());
        // On récupère le prix unitaire
        $unitPrice = $eventRegistered->getPrice();
        // On récupère les données de l'inscription
        $registration = $registrationEventRepository->findOneBy(['id' => $registrationEvent->getId()]);

        $STRIPE_KEY_PUBLIC = $this->getParameter(name: 'STRIPE_KEY_PUBLIC');

        return $this->render(view: 'stripe/order/order.html.twig', parameters: [
            'stripe_key' => $STRIPE_KEY_PUBLIC ,
            'event' => $eventRegistered ,
            'reservedPlaces' => $reservedPlaces ,
            'unitPrice' => $unitPrice ,
            'registration' => $registration ,
        ]);
    }

    #[Route('/inscription', name: 'app_confirm_without_pay', methods: ['GET' , 'POST'])]
    public function registration(
        MailService $mailService,
        Request $request,
    ): Response {
        $session = $request->getSession()->get(name: 'details_inscription', default: []);

        $mailService->sendEmail(
            from: 'contact@aperp.info',
            to: $session['representant_legal_email'],
            subject: 'Confirmation d\'inscription',
            htmlTemplate: 'emails/registration_confirm.html',
            context: [
                'inscription_id' => $session['inscription_id'],
                'representant_legal' => $session['representant_legal'],
                'evenement' => $session['evenement_nom'],
                'places_reservees' => $session['places_reservees'],
                'prix_total' => $session['total'] / 100,
                'date_de_creation' => $session['date_creation_session'],
                'evenement_lieu' => $session['evenement_lieu'],
                'evenement_debut' => $session['evenement_debut'],
                'evenement_fin' => $session['evenement_fin'],
                'evenement_date' => $session['evenement_date'],
                'date_inscription' => $session['date_inscription'],
            ]
        );

        return $this->redirectToRoute(route: 'app_home');
    }
}
