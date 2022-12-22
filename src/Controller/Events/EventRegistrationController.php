<?php

namespace App\Controller\Events;

use App\Entity\Event\Event;
use App\Entity\Event\RegistrationEvent;
use App\Form\EventRegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EventRegistrationController extends AbstractController
{
    // TODO : Renvoyer la liste des inscrits pour les ADMINS
    #[Route('/evenement/{slug}/inscription-evenement', name: 'app_event_registrations_index', methods: ['GET'])]
    public function index(Event $event): Response
    {
        $registrations = $event->getRegistrationEvents()->toArray();

        return $this->render(view: 'events/registrations/list.html.twig', parameters: [
            'event' => $event,
            'registrations' => $registrations
        ]);
    }


    #[Route(
        '/evenement/{slug}/inscription-evenement/create',
        name: 'app_event_registrations_create',
        methods: ['GET', 'POST']
    )]
    public function create(Event $event, Request $request, EntityManagerInterface $em): Response
    {
        $registration = new RegistrationEvent();

        $form = $this->createForm(type: EventRegistrationFormType::class, data: $registration);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $registration->setEvent($event);

            $em->persist($registration);
            $em->flush();

            $this->addFlash(type: 'success', message: "Merci, vous êtes inscrit !");

            return $this->redirectToRoute(
                route: 'app_event_registrations_index',
                parameters: ['event' => $event->getId()]
            );
        }

        return $this->renderForm(view: 'events/registrations/create.html.twig', parameters: [
            'event' => $event,
            'form' => $form
        ]);
    }
}
