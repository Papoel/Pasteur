<?php

namespace App\Entity\Event;

use App\Repository\Event\RegistrationEventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RegistrationEventRepository::class)]
class RegistrationEvent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\Length(
        min: 3,
        max: 100,
        minMessage: 'Le prénom doit faire plus de {{ limit }} caractères.',
        maxMessage: 'Le prénom ne doit pas faire plus de {{ limit }} caractères.'
    )]
    #[Assert\NotBlank(message: 'Le prénom ne doit pas être vide')]
    private ?string $firstname;

    #[ORM\Column(length: 100)]
    #[Assert\Length(
        min: 3,
        max: 100,
        minMessage: 'Le nom doit faire plus de {{ limit }} caractères.',
        maxMessage: 'Le nom ne doit pas faire plus de {{ limit }} caractères.'
    )]
    #[Assert\NotBlank(message: 'Le nom ne doit pas être vide')]
    private ?string $lastname;

    #[ORM\Column(length: 180)]
    #[Assert\Length(
        min: 6,
        max: 180,
        minMessage: 'L\' email doit faire plus de {{ limit }} caractères.',
        maxMessage: 'L\'email ne doit pas faire plus de {{ limit }} caractères.'
    )]
    #[Assert\NotBlank(message: 'L\'adresse email est obligatoire.')]
    #[Assert\Email]
    private ?string $email;

    #[ORM\Column(length: 10, nullable: true)]
    #[Assert\Type(type: 'numeric', message: 'Seuls les chiffres sont acceptés.')]
    #[Assert\Length(min: 10, max: 10, exactMessage: 'Le numéro de téléphone doit comporter 10 chiffres.')]
    private ?string $telephone = null;

    #[ORM\Column]
    private bool $Paid = false;

    #[ORM\ManyToOne(inversedBy: 'registrationEvents')]
    #[Assert\Valid]
    private Event $event;

    #[ORM\OneToMany(mappedBy: 'registrationEvent', targetEntity: Children::class, cascade: ['persist', 'remove'])]
    #[Assert\Count(min: 1, minMessage: 'Veuillez inscrire au moins {{ limit }} enfant.')]
    #[Assert\Valid]
    private Collection $children;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function __toString(): string
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    public function getFullname(): string
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
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

    public function isPaid(): ?bool
    {
        return $this->Paid;
    }

    public function setPaid(bool $Paid): self
    {
        $this->Paid = $Paid;

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

    /**
     * @return Collection<int, Children>
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(Children $child): self
    {
        if (!$this->children->contains($child)) {
            $this->children->add($child);
            $child->setRegistrationEvent($this);
        }

        return $this;
    }

    public function removeChild(Children $child): self
    {
        if ($this->children->removeElement($child)) {
            // set the owning side to null (unless already changed)
            if ($child->getRegistrationEvent() === $this) {
                $child->setRegistrationEvent(null);
            }
        }

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
}
