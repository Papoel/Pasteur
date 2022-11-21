<?php

declare(strict_types=1);


namespace App\Controller\Events;

use App\Entity\Event\Event;
use App\Entity\Event\Registration;
use App\Form\EventFormType;
use App\Form\PlagesHoraireFormType;
use App\Form\RegistrationHelpFormType;
use App\Repository\Event\EventRepository;
use App\Repository\PlagesHoraires\PlagesHorairesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EventRegistrationsController extends AbstractController
{
    #[Route(path: '/evenement/{slug}/inscription-aide', name: 'event_help_registration')]
    public function index(Event $event, PlagesHorairesRepository $plages): Response
    {
        return $this->render(view: 'inscriptions/index.html.twig', parameters: [
            'event' => $event,
            'registrations' => $event->getRegistrations(),
            'plages' => $plages->findByEvent(event: $event),
        ]);
    }

    #[Route(path: '/evenement/{slug}/inscription-aide/creation', name: 'event_help_registration_create', methods: ['GET', 'POST'])]
    public function create(Event $event, Request $request, EntityManagerInterface $em): Response
    {
        $registration = new Registration();

        $form = $this->createForm(type: RegistrationHelpFormType::class, data: $registration);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $registration->setEvent($event);

            $email = $registration->getEmail();
            $existingRegistration = $em->getRepository(Registration::class)->findOneBy(['email' => $email]);

            if ($existingRegistration) {
                $this->addFlash(type: 'danger', message: 'Cette adresse email est déjà enregistrée pour cet événement.');
                return $this->redirectToRoute(route: 'event_help_registration', parameters: ['slug' => $event->getSlug()]);
            }

             $em->persist($registration);
             $em->flush();

            $this->addFlash(type: 'success', message: 'Merci, votre inscription à l\'événement .$event->getName(). à bien été prise en compte.');

            return $this->redirectToRoute(route: 'event_help_registration', parameters: ['slug' => $event->getSlug()]);

        }

        return $this->render('inscriptions/create.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/evenement/{slug}/inscription-aide/test', name: 'event_help_registration_test', methods: ['GET', 'POST'])]
    public function testingform(
        Request $request,
        EntityManagerInterface $em,
        Event $event,
        EventRepository $eventRepository,
        PlagesHorairesRepository $plagesHorairesRepository
    ): Response {

        return $this->render(view: 'inscriptions/test_create.html.twig', parameters: [
            'event' => $event,
            'registrations' => $event->getRegistrations(),
            'plages' => $event->getPlagesHoraires(),
        ]);
    }




}