<?php

namespace App\Entity\Event;

use App\Repository\Event\ChildrenRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChildrenRepository::class)]
class Children
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $firstname = null;

    #[ORM\Column(length: 100)]
    private ?string $lastname = null;

    #[ORM\Column(length: 5, nullable: true)]
    private ?string $classroom = null;

    #[ORM\ManyToOne(cascade: ['persist', 'remove'] , inversedBy: 'children')]
    private ?RegistrationEvent $registrationEvent = null;

    public function __toString(): string
    {
       return $this->firstname .' '. $this->lastname .' ('. $this->classroom .')';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
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
