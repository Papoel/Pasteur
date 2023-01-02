<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\Event\Event;
use App\Entity\Event\RegistrationEvent;
use App\Repository\Event\RegistrationEventRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class DataSaveSessionServices
{
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function setData(
        Event $eventRegistered,
        RegistrationEvent $registration,
        int $reservedPlaces,
        int $unitPrice,
    ): void {
        $request = $this->requestStack->getCurrentRequest();
        $session = $request->getSession();

        $session->set('event_registered', serialize($eventRegistered));
        $session->set('registration', serialize($registration));
        $session->set('reserved_places', $reservedPlaces);
        $session->set('unit_price', $unitPrice);
        $session->set('total_price', $reservedPlaces * $unitPrice);
        $session->set('created_at', new \DateTime());
        $session->set('representant_legal', $registration->getFullname());
        $session->set('representant_legal_email', $registration->getEmail());
        $session->set('representant_legal_telephone', $registration->getTelephone());
        $session->set('inscription_event_name', $eventRegistered->getSlug());
        $session->set('event_start', $eventRegistered->getStartsAt());
    }

    public function getDatas(): array
    {
        $request = $this->requestStack->getCurrentRequest();
        $session = $request->getSession();

        $eventRegistered = unserialize(
            data: $session->get(
                name: 'event_registered'
            ),
            options: [
                    'allowed_classes' => [Event::class]
            ]
        );
        $registration = unserialize(
            data: $session->get(
                name: 'registration'
            ),
            options: [
                'allowed_classes' => [RegistrationEventRepository::class]
            ]
        );
        $reservedPlaces = $session->get(name: 'reserved_places');
        $unitPrice = $session->get(name: 'unit_price');
        $eventName = $session->get(name: 'inscription_event_name');

        return [
            'event_registered' => $eventRegistered,
            'event_start' => $session->get(name: 'event_start'),
            'registration' => $registration,
            'reserved_places' => $reservedPlaces,
            'unit_price' => $unitPrice,
            'total_price' => $reservedPlaces * $unitPrice,
            'created_at' => $session->get(name: 'created_at'),
            'representant_legal' => $session->get(name: 'representant_legal'),
            'representant_legal_email' => $session->get(name: 'representant_legal_email'),
            'representant_legal_telephone' => $session->get(name: 'representant_legal_telephone'),
            'inscription_event_name' => $eventName,
        ];
    }

    public function getData(string $key): mixed
    {
        $request = $this->requestStack->getCurrentRequest();
        $session = $request->getSession();

        return $session->get(name: $key);
    }
}
