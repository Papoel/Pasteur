<?php

namespace App\Entity\Event;

use App\Repository\Event\RegistrationHelpRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RegistrationHelpRepository::class)]
class RegistrationHelp
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'Renseigner votre pseudo ou votre prénom et nom.')]
    #[Assert\Length(
        max: 100,
        maxMessage: 'Votre Nom ne doit pas dépasser {{ value }} caractères.'
    )]
    private string $name;

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank(message: 'Renseigner votre email.')]
    #[Assert\Email]
    #[Assert\Length(
        max: 180,
        maxMessage: 'L\'email ne doit pas excéder {{ limit }} caractères.'
    )]
    private string $email;

    #[ORM\Column(length: 10, nullable: true)]
    #[Assert\Type(type: 'numeric', message: 'Seuls les chiffres sont acceptés.')]
    #[Assert\Length(min: 10, max: 10, exactMessage: 'Le numéro de téléphone doit comporter 10 chiffres.')]
    private ?string $telephone = null;

    #[ORM\Column(length: 150)]
    #[Assert\NotBlank]
    private string $activity;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $message = null;

    #[ORM\ManyToOne(inversedBy: 'registrations')]
    private Event $event;

    #[ORM\Column]
    private array $creneau_choices = [];

    public function getCreneauChoicesAsString(): string
    {
        return implode(separator: ', ', array: $this->creneau_choices);
    }

    public function __toString(): string
    {
        return $this->activity.' - '.$this->name.' ('.$this->getCreneauChoicesAsString().')';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getActivity(): string
    {
        return $this->activity;
    }

    public function setActivity(string $activity): self
    {
        $this->activity = $activity;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getEvent(): Event
    {
        return $this->event;
    }

    public function setEvent(Event $event): self
    {
        $this->event = $event;

        return $this;
    }

    public function getCreneauChoices(): array
    {
        $creneauChoices = $this->creneau_choices;

        return array_unique($creneauChoices);
    }

    public function setCreneauChoices(array $creneau_choices): self
    {
        $this->creneau_choices = $creneau_choices;

        return $this;
    }
}
