<?php

namespace App\Entity\Event;

use App\Repository\Event\ChildrenRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ChildrenRepository::class)]
class Children
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'Le prénom est obligatoire.')]
    #[Assert\Length(
        min: 3,
        max: 50,
        minMessage: 'Le prénom doit faire plus de {{ limit }} caractères.',
        maxMessage: 'Le prénom ne doit pas faire plus de {{ limit }} caractères.'
    )]
    private string $firstname;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'Le nom est obligatoire.')]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: 'Le nom doit faire plus de {{ limit }} caractères.',
        maxMessage: 'Le nom ne doit pas faire plus de {{ limit }} caractères.'
    )]
    private string $lastname;

    #[ORM\Column(length: 20, nullable: true)]
    #[Assert\Length(
        max: 20,
        maxMessage: 'Vous ne pouvez pas inscrire une classe avec plus de {{ limit }} caractères.'
    )]
    #[Assert\NotBlank(message: 'Veuillez sélectionner la classe de votre enfant (choisissez "Extérieur" si votre enfant n\'est pas de l\'école Pasteur)')]
    private ?string $classroom = null;

    #[ORM\ManyToOne(cascade: ['persist', 'remove'], inversedBy: 'children')]
    private ?RegistrationEvent $registrationEvent = null;

    public function __toString(): string
    {
        return $this->firstname . ' ' . $this->lastname . ' (' . $this->classroom . ')';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getClassroom(): ?string
    {
        return $this->classroom;
    }

    public function setClassroom(?string $classroom): self
    {
        $this->classroom = $classroom;

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
}
