<?php

namespace App\Entity\Event;

use App\Repository\Event\EventRepository;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EventRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Event
{
    public const STATUS = [
        0 => 'Projet',
        1 => 'Validé',
        2 => 'Annulé',
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 100)]
    private ?string $location = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 4, scale: 2, nullable: true)]
    private ?string $price = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $startsAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $finishAt = null;

    #[ORM\Column]
    private ?int $status = null;

    #[ORM\Column]
    private ?int $capacity = null;

    #[ORM\Column(length: 255, nullable: true, options: ['default' => 'event.jpeg'])]
    private ?string $imageFileName = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(nullable: true)]
    private ?bool $helpNeeded = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\OneToOne(inversedBy: 'event' ,targetEntity: Thumbnail::class, cascade: ['persist', 'remove'])]
    private Thumbnail $thumbnail;

    #[ORM\OneToMany(mappedBy: 'event', targetEntity: Registration::class)]
    private Collection $registrations;

    #[ORM\OneToMany(mappedBy: 'event', targetEntity: Activity::class)]
    private Collection $activities;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();

        $this->registrations = new ArrayCollection();
        $this->activities = new ArrayCollection();
    }

    #[ORM\PrePersist]
    public function prePersist()
    {
        $this->slug = (new Slugify())->slugify($this->name);
    }

    #[ORM\PreUpdate]
    public function preUpdate()
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(?float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getStartsAt(): ?\DateTimeImmutable
    {
        return $this->startsAt;
    }

    public function setStartsAt(\DateTimeImmutable $startsAt): self
    {
        $this->startsAt = $startsAt;

        return $this;
    }

    public function getFinishAt(): ?\DateTimeImmutable
    {
        return $this->finishAt;
    }

    public function setFinishAt(\DateTimeImmutable $finishAt): self
    {
        $this->finishAt = $finishAt;

        return $this;
    }

    public function getStatus(): string
    {
        return self::STATUS[$this->status];
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    public function setCapacity(int $capacity): self
    {
        $this->capacity = $capacity;

        return $this;
    }

    public function getImageFileName(): ?string
    {
        return $this->imageFileName;
    }

    public function setImageFileName(?string $imageFileName): self
    {
        $this->imageFileName = $imageFileName;

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

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function isHelpNeeded(): ?bool
    {
        return $this->helpNeeded;
    }

    public function setHelpNeeded(?bool $helpNeeded): self
    {
        $this->helpNeeded = $helpNeeded;

        return $this;
    }

    public function isFree(): bool
    {
        return (0 == $this->getPrice()) || is_null($this->getPrice());
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection<int, Registration>
     */
    public function getRegistrations(): Collection
    {
        return $this->registrations;
    }

    public function addRegistration(Registration $registration): self
    {
        if (!$this->registrations->contains($registration)) {
            $this->registrations->add($registration);
            $registration->setEvent($this);
        }

        return $this;
    }

    public function removeRegistration(Registration $registration): self
    {
        if ($this->registrations->removeElement($registration)) {
            // set the owning side to null (unless already changed)
            if ($registration->getEvent() === $this) {
                $registration->setEvent(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function getThumbnail(): ?Thumbnail
    {
        return $this->thumbnail;
    }

    public function setThumbnail(?Thumbnail $thumbnail): self
    {
        $this->thumbnail = $thumbnail;

        return $this;
    }

    /**
     * @return Collection<int, Activity>
     */
    public function getActivities(): Collection
    {
        return $this->activities;
    }

    public function addActivity(Activity $activity): self
    {
        if (!$this->activities->contains($activity)) {
            $this->activities->add($activity);
            $activity->setEvent($this);
        }

        return $this;
    }

    public function removeActivity(Activity $activity): self
    {
        if ($this->activities->removeElement($activity)) {
            // set the owning side to null (unless already changed)
            if ($activity->getEvent() === $this) {
                $activity->setEvent(null);
            }
        }

        return $this;
    }
}
