<?php

namespace App\Entity\Event;

use App\Entity\Slot\Slot;
use App\Entity\Payment;
use App\Repository\Event\EventRepository;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Uid\Uuid;
use Vich\UploaderBundle\Mapping\Annotation\Uploadable;
use Vich\UploaderBundle\Mapping\Annotation\UploadableField;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EventRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[Uploadable]
#[UniqueEntity(
    fields: ['slug', 'startsAt'],
    message: 'L\'événement {{ value }} est déjà programmé à cette date et heure.'
)]
class Event
{
    public const STATUS = ['PREPARATION', 'RUNNING', 'FINISHED'];

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Assert\Uuid]
    private ?Uuid $id = null;

    #[ORM\Column(type: 'string', length: 150)]
    #[Assert\NotBlank(message: 'Veuillez renseigner un titre pour cet événement.')]
    #[Assert\Length(
        min: 3,
        max: 150,
        minMessage: 'Le titre doit comporter au moins {{ limit }} caractères.',
        maxMessage: 'Le titre ne peut pas comporter plus de {{ limit }} caractères.'
    )]
    private string $name;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\NotBlank]
    private ?string $description = null;

    #[ORM\Column(length: 100)]
    #[Assert\NotBlank(message: 'Veuillez renseigner un lieu.')]
    #[Assert\Length(
        min: 3,
        max: 100,
        minMessage: 'Le lieu doit comporter au moins {{ limit }} caractères.',
        maxMessage: 'Le lieu ne peut pas comporter plus de {{ limit }} caractères.'
    )]
    private string $location;

    #[ORM\Column(type: Types::INTEGER, precision: 4, scale: 2, nullable: true)]
    #[Assert\GreaterThanOrEqual(0)]
    #[Assert\LessThan(
        10000,
        message: 'Le prix ne doit pas dépasser {{ value }} €'
    )]
    private ?int $price = null;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Assert\NotBlank]
    private ?\DateTimeImmutable $startsAt = null;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Assert\NotBlank]
    #[Assert\GreaterThan(
        propertyPath: 'startsAt',
        message: 'La date de fin doit être supérieur à la dae de début.'
    )]
    private ?\DateTimeImmutable $finishAt = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $status = Event::STATUS[0];

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Assert\PositiveOrZero]
    private ?int $capacity = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $slug;

    #[ORM\Column(nullable: true)]
    private ?bool $helpNeeded = null;

    #[ORM\Column]
    private bool $published = false;

    #[UploadableField(mapping: 'event_image', fileNameProperty: 'imageName')]
    private ?File $imageFile = null;

    #[ORM\Column(type: 'string', nullable: true, options: ['default' => 'event.jpeg'])]
    private ?string $imageName = 'event.jpeg';

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToMany(targetEntity: Slot::class, inversedBy: 'events')]
    #[ORM\JoinTable(name: 'slot_event')]
    private Collection $creneaux;

    #[ORM\OneToMany(mappedBy: 'event', targetEntity: RegistrationHelp::class)]
    private Collection $registrations;

    #[ORM\OneToMany(mappedBy: 'event', targetEntity: RegistrationEvent::class, cascade: ['remove'])]
    private Collection $registrationEvents;

    #[ORM\OneToMany(mappedBy: 'event', targetEntity: Payment::class)]
    private Collection $payments;

    #[ORM\Column(nullable: true)]
    private ?int $registered = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();

        $this->registrations = new ArrayCollection();
        $this->creneaux = new ArrayCollection();
        $this->registrationEvents = new ArrayCollection();
        $this->payments = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->name;
    }

    #[ORM\PreUpdate]
    public function preUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    #[ORM\PrePersist]
    public function prePersist(): void
    {
        $this->slug = (new Slugify())->slugify($this->name);
    }

    public function getId(): ?Uuid
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

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    public function setLocation(string $location): self
    {
        $this->location = $location;

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
        return $this->status;
    }

    public function setStatus(string $status): self
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

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function isHelpNeeded(): bool
    {
        return $this->helpNeeded;
    }

    public function setHelpNeeded(?bool $helpNeeded): self
    {
        $this->helpNeeded = $helpNeeded;

        return $this;
    }

    public function isPublished(): bool
    {
        return $this->published;
    }

    public function setPublished(bool $published): self
    {
        $this->published = $published;

        return $this;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
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

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(?int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getRegistrations(): Collection
    {
        return $this->registrations;
    }

    public function addRegistration(RegistrationHelp $registration): self
    {
        if (!$this->registrations->contains($registration)) {
            $this->registrations->add($registration);
            $registration->setEvent($this);
        }

        return $this;
    }

    public function removeRegistration(RegistrationHelp $registration): self
    {
        if ($this->registrations->removeElement($registration)) {
            // set the owning side to null (unless already changed)
            if ($registration->getEvent() === $this) {
                $registration->setEvent(event: null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Slot>
     */
    public function getCreneaux(): Collection
    {
        return $this->creneaux;
    }

    public function addCreneaux(Slot $creneaux): self
    {
        if (!$this->creneaux->contains($creneaux)) {
            $this->creneaux->add($creneaux);
        }

        return $this;
    }

    public function removeCreneaux(Slot $creneaux): self
    {
        $this->creneaux->removeElement($creneaux);

        return $this;
    }

    /**
     * @return Collection<int, RegistrationEvent>
     */
    public function getRegistrationEvents(): Collection
    {
        return $this->registrationEvents;
    }

    public function addRegistrationEvent(RegistrationEvent $registrationEvent): self
    {
        if (!$this->registrationEvents->contains($registrationEvent)) {
            $this->registrationEvents->add($registrationEvent);
            $registrationEvent->setEvent($this);
        }

        return $this;
    }

    public function removeRegistrationEvent(RegistrationEvent $registrationEvent): self
    {
        if ($this->registrationEvents->removeElement($registrationEvent)) {
            // set the owning side to null (unless already changed)
            if ($registrationEvent->getEvent() === $this) {
                $registrationEvent->setEvent(null);
            }
        }

        return $this;
    }

    /**
     * Vérifiez si l'événement est gratuit ou non.
     */
    public function isFree(): bool
    {
        return (0 == $this->getPrice()) || is_null($this->getPrice());
    }

    /**
     * Le nombre de places restantes pour cet événement.
     */
    public function getSpotsLeft(): int
    {
        return $this->getCapacity() - $this->getRegistrationEvents()->count();
    }

    /**
     * Vérifiez s'il n'y a plus de places disponibles pour cet événement.
     */
    public function isSoldOut(): bool
    {
        return 0 === $this->getSpotsLeft();
    }

    /**
     * @return Collection<int, Payment>
     */
    public function getPayments(): Collection
    {
        return $this->payments;
    }

    public function addPayment(Payment $payment): self
    {
        if (!$this->payments->contains($payment)) {
            $this->payments->add($payment);
            $payment->setEvent($this);
        }

        return $this;
    }

    public function removePayment(Payment $payment): self
    {
        if ($this->payments->removeElement($payment)) {
            // set the owning side to null (unless already changed)
            if ($payment->getEvent() === $this) {
                $payment->setEvent(null);
            }
        }

        return $this;
    }

    public function getRegistered(): ?int
    {
        return $this->registered;
    }

    public function setRegistered(?int $registered): self
    {
        $this->registered = $registered;

        return $this;
    }
}
