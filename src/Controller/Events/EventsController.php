<?php

namespace App\Controller\Events;

use App\Entity\Event\Event;
use App\Repository\Event\EventRepository;
use App\Repository\Product\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EventsController extends AbstractController
{
    #[Route('/evenements', name: 'app_events')]
    public function index(EventRepository $eventRepository, ProductRepository $productRepository): Response
    {
        $events = $eventRepository->findUpcoming();
        $eventsPublished = $eventRepository->countPublishedEvents() + $productRepository->countPublishedProducts();

        $products = $productRepository->findUpComing();

        return $this->render(view: 'events/index.html.twig', parameters: [
            'events' => $events,
            'eventsPublished' => $eventsPublished,
            'products' => $products,
        ]);
    }

    #[Route('/evenement/{slug}', name: 'app_event_show')]
    public function show(Event $event, EventRepository $eventRepository): Response
    {
        $events = $eventRepository->findUpcoming();

        return $this->render(view: 'events/show.html.twig', parameters: compact('event', 'events'));
    }
}
