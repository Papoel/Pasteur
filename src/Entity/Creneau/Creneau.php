<?php

namespace App\Entity\Creneau;

use App\Entity\Event\Event;
use App\Repository\Creneau\CreneauRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinTable;

#[ORM\Entity(repositoryClass: CreneauRepository::class)]
class Creneau
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $startsAt = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $endsAt = null;

    #[ORM\ManyToMany(targetEntity: Event::class, inversedBy: 'creneaux')]
    private Collection $event;

    public function __construct()
    {
        $this->event = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartsAt(): ?\DateTimeInterface
    {
        return $this->startsAt;
    }

    public function setStartsAt(\DateTimeInterface $startsAt): self
    {
        $this->startsAt = $startsAt;

        return $this;
    }

    public function getEndsAt(): ?\DateTimeInterface
    {
        return $this->endsAt;
    }

    public function setEndsAt(\DateTimeInterface $endsAt): self
    {
        $this->endsAt = $endsAt;

        return $this;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getEvent(): Collection
    {
        return $this->event;
    }

    public function addEvent(Event $event): self
    {
        if (!$this->event->contains($event)) {
            $this->event->add($event);
        }

        return $this;
    }

    public function removeEvent(Event $event): self
    {
        $this->event->removeElement($event);

        return $this;
    }

    public function getPlage(Event $event): bool
    {
        return $this->startsAt->format(format: 'H:i') . ' - ' . $this->endsAt->format(format: 'H:i');
    }

    public function __toString(): string
    {
        return $this->startsAt->format(format: 'H:i') . ' - ' . $this->endsAt->format(format: 'H:i');
    }
}
