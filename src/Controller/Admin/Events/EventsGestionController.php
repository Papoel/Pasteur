<?php

declare(strict_types=1);

namespace App\Controller\Admin\Events;

use App\Entity\Event\Event;
use App\Repository\Event\EventRepository;
use App\Repository\Event\RegistrationEventRepository;
use App\Repository\Event\RegistrationHelpRepository;
use App\Services\PdfService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class EventsGestionController extends AbstractController
{
    #[Route('/admin/details/evenements', name: 'app_admin_details_events', methods: ['GET'])]
    #[isGranted('ROLE_ADMIN')]
    public function detailsEvents(
        EventRepository $eventRepository,
        RegistrationEventRepository $registrations ,
        RegistrationHelpRepository $registrationHelpRepository,
    ): Response {

        return $this->render(view: 'admin/events/details_events.html.twig', parameters: [
            'events' => $eventRepository->findAll(),
            'registrations' => $registrations->findAll(),
            'registrationHelps' => $registrationHelpRepository->findBy(['event' => $eventRepository->findAll()])
        ]);
    }

    #[Route('/admin/details/inscription/{slug}', name: 'app_admin_details_registration', methods: ['GET'])]
    #[isGranted('ROLE_ADMIN')]
    public function detailsRegistration(
        Event $event,
        RegistrationEventRepository $registrationRepository,
        RegistrationHelpRepository $registrationHelpRepository
    ): Response {
        $registrations = $registrationRepository->findBy(['event' => $event]);

        // Get unique people registered search by firstname and lastname
        $uniqueRegistrations = [];
        foreach ($registrations as $registration) {
            $uniqueRegistrations[$registration->getFirstname() . $registration->getLastname()] = $registration;
        }
        $uniqueRegistrations = array_values($uniqueRegistrations);

        $registrationHelps = $registrationHelpRepository->findBy(['event' => $event]);

        return $this->render(view: 'admin/events/details_registration.html.twig', parameters: [
            'registrations' => $registrations ,
            'uniqueRegistrations' => $uniqueRegistrations ,
            'event' => $event ,
            'registrationHelps' => $registrationHelps ,
        ]);
    }

    #[Route('/admin/details/inscription/{slug}/pdf', name: 'app_admin_list_pdf', methods: ['GET'])]
    #[isGranted('ROLE_ADMIN')]
    public function generatePdf(
        Event $event,
        RegistrationEventRepository $registrations,
        PdfService $pdf
    ): Response {
        $html = $this->render(view: 'admin/pdf/liste.html.twig', parameters: [
            'registrations' => $registrations->findBy(['event' => $event], ['lastname' => 'ASC']),
            'event' => $event ,
        ]);
        $eventName = $event->getName();

        $pdf->showPdf($html, $eventName);
    }
}
