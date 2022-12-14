<?php

declare(strict_types=1);

namespace App\Controller\Events;

use App\Entity\Event\Event;
use App\Entity\Event\Registration;
use App\Form\RegistrationHelpFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EventRegistrationsController extends AbstractController
{
    #[Route(
        path: '/evenement/{slug}/inscription-aide/creation',
        name: 'event_help_registration_create',
        methods: ['GET', 'POST']
    )]
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

            // 1. Je récupère l'email de l'aidant
            $email = $registration->getEmail();
            // 2. Je récupère toutes les inscriptions de l'aidant à l'événement
            $existingRegistration = $em->getRepository(
                entityName: Registration::class
            )->findBy(
                ['email' => $email, 'event' => $event, 'activity' => $form->getData()->getActivity()]
            );

            // Vérifier les valeurs du tableau $registrationByActivity et les comparer avec les valeurs Activity
            // de $existingRegistration
            // Si les valeurs sont identiques, alors on ne peut pas créer l'inscription
            if ($existingRegistration) {
                $this->addFlash(type: 'danger', message: 'Vous êtes déjà inscrit à cette activité');

                return $this->redirectToRoute(route: 'app_event_show', parameters: ['slug' => $event->getSlug()]);
            }

            // Récupérer dans un tableau les créneaux sélectionnés et faire un formatage pour les insérer dans
            // la table registration
            $creneau_choices = $form->get('creneauChoices')->getData();
            $creneau_choices = array_map(
                static function ($creneau) {
                    return $creneau->getStartsAt()->format('H:i') . ' - ' . $creneau->getEndsAt()->format('H:i');
                },
                $creneau_choices
            );

            $registration->setCreneauChoices($creneau_choices);

            $em->persist($registration);
            $em->flush();

            $this->addFlash(
                type: 'success',
                message: 'Merci, votre inscription à l\'événement : '
                . $event->getName() .
                ' à bien été prise en compte.'
            );

            return $this->redirectToRoute(route: 'app_event_show', parameters: ['slug' => $event->getSlug()]);
        }

        return $this->render(view: 'inscriptions/create.html.twig', parameters: [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }
}
