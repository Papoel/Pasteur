<?php

declare(strict_types=1);


namespace App\Controller\Events;

use App\Entity\Event\Event;
use App\Entity\Event\Registration;
use App\Form\EventFormType;
use App\Form\RegistrationHelpFormType;
use App\Repository\Creneau\CreneauRepository;
use App\Repository\Event\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EventRegistrationsController extends AbstractController
{
    #[Route(path: '/evenement/{slug}/inscription-aide', name: 'event_help_registration')]
    public function index(Event $event, CreneauRepository $creneau): Response
    {
        return $this->render(view: 'inscriptions/index.html.twig', parameters: [
            'event' => $event,
            'registrations' => $event->getRegistrations(),
            'creneaux' => $event->getCreneaux(),
        ]);
    }

    #[Route(path: '/evenement/{slug}/inscription-aide/creation', name: 'event_help_registration_create', methods: ['GET', 'POST'])]
    public function create(Event $event, Request $request, EntityManagerInterface $em): Response
    {
        $registration = new Registration();
        $event_creneaux = $event->getCreneaux()->toArray();

        $form = $this->createForm(type: RegistrationHelpFormType::class, data: $registration, options: [
            'event_creneaux' => $event_creneaux,
        ]);

        $form->handleRequest($request);
        // dd($event->getCreneaux()->toArray());


        if ($form->isSubmitted() && $form->isValid()) {
            $registration->setEvent($event);

            $email = $registration->getEmail();
            $existingRegistration = $em->getRepository(entityName: Registration::class)->findOneBy(['email' => $email]);

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
}
