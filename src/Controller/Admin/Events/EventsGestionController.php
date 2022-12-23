<?php

declare(strict_types=1);

namespace App\Controller\Admin\Events;

use App\Entity\Event\Event;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EventsGestionController extends AbstractController
{
// TODO : A retravailler -> Renvoie la liste des inscrits pour les ADMINS
    #[Route('/evenement/{slug}/inscription-evenement', name: 'app_event_registrations_index', methods: ['GET'])]
    public function index(Event $event): Response
    {
        $registrations = $event->getRegistrationEvents()->toArray();

        return $this->render(view: 'events/registrations/list.html.twig', parameters: [
            'event' => $event,
            'registrations' => $registrations,
        ]);
    }
}
