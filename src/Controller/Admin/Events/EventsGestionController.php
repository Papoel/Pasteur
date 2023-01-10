<?php

declare(strict_types=1);

namespace App\Controller\Admin\Events;

use App\Entity\Event\Event;
use App\Repository\Event\EventRepository;
use App\Repository\Event\RegistrationEventRepository;
use App\Services\PdfService;
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
        return $this->render(view: 'admin/events/details_registration.html.twig', parameters: [
            'registrations' => $registrations->findBy(['event' => $event]),
            'event' => $event,
        ]);
    }

    #[Route('/admin/details/inscription/{slug}/pdf', name: 'app_admin_list_pdf', methods: ['GET'])]
    public function generatePdf(
        Event $event,
        RegistrationEventRepository $registrations,
        PdfService $pdf
    ): Response {
        $html = $this->render(view: 'admin/pdf/liste.html.twig', parameters: [
            'registrations' => $registrations->findBy(['event' => $event]),
            'event' => $event,
        ]);
        $eventName = $event->getName();

        $pdf->showPdf($html, $eventName);
    }
}
