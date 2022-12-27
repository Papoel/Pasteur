<?php

namespace App\Controller\Events;

use App\Entity\Event\Event;
use App\Entity\Event\RegistrationEvent;
use App\Entity\User\User;
use App\Form\EventRegistrationFormType;
use App\Repository\User\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EventRegistrationController extends AbstractController
{
    #[Route(
        '/evenement/inscription/{slug}/create',
        name: 'app_event_registrations_create',
        methods: ['GET' , 'POST']
    )]
    public function create(
        Event $event,
        Request $request,
        EntityManagerInterface $em,
        UserRepository $userRepository
    ): Response {
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
            /** @var User $userFirstname */
            $registration->setFirstname($userFirstname);
            /** @var User $userLastname */
            $registration->setLastname($userLastname);
            /** @var User $userEmail */
            $registration->setEmail($userEmail);
            /** @var User $userTelephone */
            $registration->setTelephone($userTelephone);
        }

        $form = $this->createForm(type: EventRegistrationFormType::class, data: $registration);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reservedPlaces = count($form->getData()->getChildren());

            if ($reservedPlaces <= $event->getCapacity()) {
                $remainingPlaces = $event->getCapacity() - $reservedPlaces;
                $event->setCapacity($remainingPlaces);
                $registration->setEvent($event);

                $em->persist($registration);
                $em->flush();

                $this->addFlash(type: 'success', message: 'Merci, vous êtes inscrit !');

                return $this->redirectToRoute(
                    route: 'app_event_show',
                    parameters: ['slug' => $event->getSlug()]
                );
            }

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
}
