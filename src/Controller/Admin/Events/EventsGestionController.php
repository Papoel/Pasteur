<?php

declare(strict_types=1);

namespace App\Controller\Admin\Events;

use App\Entity\Event\Event;
use App\Repository\Event\EventRepository;
use App\Repository\Event\RegistrationEventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EventsGestionController extends AbstractController
{
    #[Route('/admin/details/evenements', name: 'app_admin_details_events', methods: ['GET'])]
    public function detailsEvents(
        EventRepository $eventRepository,
        RegistrationEventRepository $registrations,
    ): Response {
        $events = $eventRepository->findAll();
        $registrations = $registrations->findAll();

        return $this->render(view: 'admin/events/details_events.html.twig', parameters: [
            'events' => $events,
            'registrations' => $registrations,
        ]);
    }

    #[Route('/admin/details/inscription/{slug}', name: 'app_admin_details_registration', methods: ['GET'])]
    public function detailsRegistration(Event $event, RegistrationEventRepository $registrations,): Response
    {
        $registrations = $registrations->findBy(['event' => $event]);

        return $this->render(view: 'admin/events/details_registration.html.twig', parameters: [
            'registrations' => $registrations ,
            'event' => $event,
        ]);
    }
}
