<?php

namespace App\Controller\Events;

use App\Entity\Event\Event;
use App\Entity\Event\RegistrationEvent;
use App\Entity\User\User;
use App\Form\EventRegistrationFormType;
use App\Repository\Event\ChildrenRepository;
use App\Repository\Event\EventRepository;
use App\Repository\Event\RegistrationEventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EventRegistrationController extends AbstractController
{
    #[Route('/evenement/inscription/{slug}/create', name: 'app_event_registrations_create', methods: ['GET' , 'POST'])]
    public function create(Event $event, Request $request, EntityManagerInterface $em,): Response
    {
        $registration = new RegistrationEvent();

        if ($this->getUser()) {
            /** @var User $currentUser */
            $currentUser = $this->getUser();
            $userFirstname = $currentUser->getFirstname();
            $userLastname = $currentUser->getLastname();
            $userEmail = $currentUser->getEmail();
            $userTelephone = $currentUser->getTelephone();
        }

        // Renseigné les informations de l'utilisateur connecté dans le formulaire
        if ($this->getUser()) {
            /* @var User $userFirstname */
            $registration->setFirstname($userFirstname);
            /* @var User $userLastname */
            $registration->setLastname($userLastname);
            /* @var User $userEmail */
            $registration->setEmail($userEmail);
            /* @var User $userTelephone */
            $registration->setTelephone($userTelephone);
        }

        $form = $this->createForm(type: EventRegistrationFormType::class, data: $registration);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reservedPlaces = count($form->getData()->getChildren());

            // On s'assure que le nombre de places réservées ne dépasse pas le nombre de places disponibles
            if ($reservedPlaces <= $event->getCapacity()) {
                // On récupère le nombre de places déjà réservées
                $remainingPlaces = $event->getCapacity() - $reservedPlaces;
                // On met à jour le nombre de places restantes
                $event->setCapacity($remainingPlaces);
                $event->setRegistered(registered: $event->getRegistered() + $reservedPlaces);
                // On enregistre la réservation
                $registration->setEvent($event);

                $em->persist($registration);
                $em->flush();

                // On enregistre les données dans la session
                $details_inscription = $request->getSession()->get(name: 'details_inscription', default: []);

                // Créer une session pour enregistrer les données de l'inscription si elle n'existe pas
                if (empty($details_inscription)) {
                    $details_inscription = [
                        'id' => uniqid(prefix: '', more_entropy: false),
                        // set the dateTime now with the format Y-m-d H:i:s
                        'stripe_session_id' => '',
                        'date_creation_session' => date(format: 'd-m-Y_H:i:s'),
                        'evenement_id' => $event->getId(),
                        'evenement_nom' => $event->getName(),
                        'evenement_slug' => $event->getSlug(),
                        'evenement_prix' => $event->getPrice(),
                        'inscription_id' => $registration->getId(),
                        'representant_legal' => $registration->getFullname(),
                        'representant_legal_email' => $registration->getEmail(),
                        'representant_legal_telephone' => $registration->getTelephone(),
                        'date_inscription' => $registration->getCreatedAt()->format(format: 'd/m/Y H:i:s'),
                        'places_reservees' => $reservedPlaces,
                        'total' => $event->getPrice() * $reservedPlaces,
                        'payer' => false,
                        'evenement' => $event,
                        'inscription' => $registration,
                    ];
                    $request->getSession()->set(name: 'details_inscription', value: $details_inscription);
                }

                // Une fois la réservation enregistrée, on redirige l'utilisateur vers la page de paiement en lui
                // passant l'ID de la réservation
                return $this->redirectToRoute(route: 'app_session_payment', parameters:
                    [
                        'id' => $registration->getId() ,
                        'event' => $registration->getEvent()->getSlug() ,
                        'reservedPlaces' => $reservedPlaces
                    ]);

                $this->addFlash(type: 'Merci, vous êtes inscrit !', message: 'success');

                return $this->redirectToRoute(
                    route: 'app_event_show',
                    parameters: ['id' => $registration->getId()]
                );
            }

            // Si le nombre de places réservées dépasse le nombre de places disponibles ⇒ message d'erreur
            $this->addFlash(type: 'danger', message: sprintf(
                'Désolé vous avez tenté d\'inscrire %s enfant(s) alors qu\'il ne reste que %s place(s) de disponible.',
                $reservedPlaces,
                $event->getCapacity(),
            ));
        }

        return $this->renderForm(view: 'events/registrations/registration-event.html.twig', parameters: [
            'event' => $event ,
            'form' => $form ,
        ]);
    }

    #[Route('/annulation/{id}/evenement/{slug}', name: 'app_registration_event_cancel', methods: ['GET' , 'POST'])]
    public function cancelRegistrationEvent(
        RegistrationEventRepository $registrationEventRepository,
        ChildrenRepository $childrenRepository,
        EventRepository $eventRepository,
        EntityManagerInterface $em,
        Request $request
    ): Response {
        // Récupère l'ID de l'événement à partir du slug dans l'URL
        $registrationId = $request->attributes->get(key: 'id');

        // Récupère l'entité Event
        $event = $eventRepository->findOneBy(['slug' => $request->attributes->get(key: 'slug')]);

        // Récupère l'entité EventRegistration
        $registrationEvent = $registrationEventRepository->findOneBy(['id' => $registrationId]);

        // Récupère les enfants associés à l'EventRegistration
        $children = $childrenRepository->findBy(['registrationEvent' => $registrationEvent]);

        // Met à jour le nombre de places disponibles dans l'entité Event
        $event->setCapacity(capacity: $event->getCapacity() + count($children));
        // Mettre à jour le nombre d'inscrits dans l'entité registered
        $event->setRegistered(registered: $event->getRegistered() - count($children));

        // Supprime chaque enfant de la table Children
        foreach ($children as $child) {
            $em->remove($child);
        }

        $em->remove($registrationEvent);
        $em->flush();

        // Affiche un message de confirmation
        $this->addFlash(type: 'success', message: 'Votre inscription a bien été annulée.');
        // Redirige l'utilisateur vers la page de confirmation de suppression
        return $this->redirectToRoute(route: 'app_home');
    }
}
