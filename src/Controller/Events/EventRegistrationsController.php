<?php

declare(strict_types=1);


namespace App\Controller\Events;

use App\Entity\Event\Event;
use App\Entity\Event\Registration;
use App\Entity\PlagesHoraires\PlagesHoraires;
use App\Form\RegistrationHelpFormType;
use App\Repository\Event\EventRepository;
use App\Repository\PlagesHoraires\PlagesHorairesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EventRegistrationsController extends AbstractController
{
    #[Route(path: '/evenement/{slug}/inscription-aide', name: 'event_help_registration')]
    public function index(Event $event, PlagesHorairesRepository $plages): Response
    {
       dd('dd($event->getPlagesHoraires)', $event->getPlagesHoraires());


        return $this->render(view: 'inscriptions/index.html.twig', parameters: [
            'event' => $event,
            'registrations' => $event->getRegistrations(),
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
            'form' => $form->createView()
        ]);
    }

}
