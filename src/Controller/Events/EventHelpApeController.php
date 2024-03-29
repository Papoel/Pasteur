<?php

declare(strict_types=1);

namespace App\Controller\Events;

use App\Entity\Event\Event;
use App\Entity\Event\RegistrationHelp;
use App\Entity\User\User;
use App\Form\RegistrationHelpFormType;
use App\Repository\Event\RegistrationHelpRepository;
use App\Repository\User\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EventHelpApeController extends AbstractController
{
    #[Route(
        path: '/evenement/{slug}/inscription-aide/creation',
        name: 'event_help_registration_create',
        methods: ['GET', 'POST']
    )]
    public function create(
        Event $event,
        Request $request,
        EntityManagerInterface $em,
        UserRepository $userRepository
    ): Response {
        $registration = new RegistrationHelp();
        $event_creneaux = $event->getCreneaux()->toArray();

        /** @var User $currentUser */
        $currentUser = $this->getUser();

        if ($currentUser) {
            $userName = $userRepository->find($currentUser)->getFullName();
            $userEmail = $userRepository->find($currentUser)->getEmail();
            $userTelephone = $userRepository->find($currentUser)->getTelephone();
        }

        // Renseigné les informations de l'utilisateur connecté dans le formulaire
        if ($currentUser) {
            $registration->setName($userName);
            $registration->setEmail($userEmail);
            $registration->setTelephone($userTelephone);
        }

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
                entityName: RegistrationHelp::class
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

    #[Route(
        path: '/annulation/{id}/organisation-evenement/{slug}',
        name: 'app_event_registration_help_cancel',
        methods: ['GET', 'POST']
    )]
    public function cancelRegistrationHelp(
        Event $event,
        RegistrationHelpRepository $helpRepository,
        EntityManagerInterface $em,
    ): Response {
        $help = $helpRepository->find($event);

         $em->remove($help);
         $em->flush();

         $this->addFlash(type: 'success', message: 'L\inscription a bien été annulée');

        return $this->redirectToRoute(
            route: 'app_admin_details_registration',
            parameters: [
                'slug' => $help->getEvent()->getSlug()
            ]
        );
    }
}
