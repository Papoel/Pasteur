<?php

declare(strict_types=1);

namespace App\Controller\Admin\Events;

use App\Repository\Event\EventRepository;
use App\Repository\Event\RegistrationEventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EventsGestionController extends AbstractController
{
    // TODO : A retravailler -> Renvoie la liste des inscrits pour les ADMINS
    #[Route('/admin/details/inscription', name: 'app_event_registrations_index', methods: ['GET'])]
    public function index(EventRepository $eventRepository, RegistrationEventRepository $registrations): Response
    {
        $events = $eventRepository->findAll();
        $registrations = $registrations->findAll();

        return $this->render(view: 'events/registrations/list.html.twig', parameters: [
            'events' => $events,
            'registrations' => $registrations,
        ]);
    }
}
