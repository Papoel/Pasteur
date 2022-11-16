<?php

namespace App\Controller\Events;

use App\Entity\Event;
use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EventsController extends AbstractController
{
    #[Route('/evenements', name: 'app_events')]
    public function index(EventRepository $eventRepository): Response
    {
        $events = $eventRepository->findUpcomming();

        return $this->render(view: 'events/index.html.twig', parameters: compact(var_name: 'events'));
    }

    #[Route('/evenement/{slug}', name: 'app_event_show')]
    public function show(Event $event): Response
    {
        //dd($event);
        return $this->render('events/show.html.twig', compact('event'));
    }
}
