<?php

namespace App\Controller\Payment;

use App\Entity\Event\RegistrationEvent;
use App\Repository\Event\EventRepository;
use App\Repository\Event\RegistrationEventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RecapPaymentController extends AbstractController
{
    #[Route('/inscription/{event}/{id}', name: 'app_session_payment', methods: ['GET' , 'POST'])]
    public function validate(
        RegistrationEvent $registrationEvent,
        EventRepository $eventRepository,
        RegistrationEventRepository $registrationEventRepository
    ): Response {
        // On récupère l'événement
        $eventRegistered = $eventRepository->findOneBy(['slug' => $registrationEvent->getEvent()->getSlug()]);
        // On récupère le nombre de places réservées
        $reservedPlaces = count($registrationEvent->getChildren());
        // On récupère le prix unitaire
        $unitPrice = $eventRegistered->getPrice();
        // On récupère les données de l'inscription
        $registration = $registrationEventRepository->findOneBy(['id' => $registrationEvent->getId()]);

        // dd($details_inscription);

        $STRIPE_KEY_PUBLIC = $this->getParameter(name: 'STRIPE_KEY_PUBLIC');

        $getSession = $this->dataSaveSessionServices->getDatas();
        // dd($getSession);

        return $this->render(view: 'stripe/recap.html.twig', parameters: [
            'stripe_key' => $STRIPE_KEY_PUBLIC ,
            'event' => $eventRegistered ,
            'reservedPlaces' => $reservedPlaces ,
            'unitPrice' => $unitPrice ,
            'registration' => $registration ,
        ]);
    }
}
