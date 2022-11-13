<?php

namespace App\Controller;

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
        // get all events
        $events = $eventRepository->findUpcomming();
        return $this->render(view: 'events/index.html.twig', parameters: compact(var_name: 'events'));
    }
}
