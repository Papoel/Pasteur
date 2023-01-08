<?php

namespace App\Entity;

use App\Entity\Event\Event;
use App\Entity\Event\RegistrationEvent;
use App\Repository\PaymentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaymentRepository::class)]
class Payment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $stripeSessionId = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\ManyToOne(inversedBy: 'payments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?RegistrationEvent $registrationEvent = null;

    #[ORM\ManyToOne(inversedBy: 'payments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Event $event = null;

    #[ORM\Column(length: 255)]
    private ?string $stripePaymentIntentId = null;

    #[ORM\Column(length: 100)]
    private ?string $stripePaymentIntentStatus = null;

    #[ORM\Column]
    private ?int $reservedPlaces = null;

    #[ORM\Column]
    private ?int $amount = null;

    #[ORM\Column]
    private ?int $unit_price = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStripeSessionId(): ?string
    {
        return $this->stripeSessionId;
    }

    public function setStripeSessionId(string $stripeSessionId): self
    {
        $this->stripeSessionId = $stripeSessionId;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getRegistrationEvent(): ?RegistrationEvent
    {
        return $this->registrationEvent;
    }

    public function setRegistrationEvent(?RegistrationEvent $registrationEvent): self
    {
        $this->registrationEvent = $registrationEvent;

        return $this;
    }

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(?Event $event): self
    {
        $this->event = $event;

        return $this;
    }

    public function getStripePaymentIntentId(): ?string
    {
        return $this->stripePaymentIntentId;
    }

    public function setStripePaymentIntentId(?string $stripePaymentIntentId): self
    {
        $this->stripePaymentIntentId = $stripePaymentIntentId;

        return $this;
    }

    public function getStripePaymentIntentStatus(): ?string
    {
        return $this->stripePaymentIntentStatus;
    }

    public function setStripePaymentIntentStatus(string $stripePaymentIntentStatus): self
    {
        $this->stripePaymentIntentStatus = $stripePaymentIntentStatus;

        return $this;
    }

    public function getReservedPlaces(): ?int
    {
        return $this->reservedPlaces;
    }

    public function setReservedPlaces(int $reservedPlaces): self
    {
        $this->reservedPlaces = $reservedPlaces;

        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getUnitPrice(): ?int
    {
        return $this->unit_price;
    }

    public function setUnitPrice(int $unit_price): self
    {
        $this->unit_price = $unit_price;

        return $this;
    }
}
