<?php

namespace App\Entity\User;

use App\Repository\User\UserRepository;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: ['email'], message: 'Un compte existe déjà avec cette adresse email.')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Assert\Uuid]
    private Uuid $id;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank(message: 'L\'email est obligatoire')]
    #[Assert\Email(
        message: 'L\' adresse email {{ value }} n\'est pas valide',
    )]
    private string $email;

    #[ORM\Column]
    private array $roles = ['ROLE_USER'];

    private ?string $plainPassword;

    #[ORM\Column(type: 'string')]
    private string $password;

    #[ORM\Column(length: 50)]
    #[Assert\Length(
        min: 3,
        max: 50,
        minMessage: 'Le prénom doit faire au moins {{ limit }} caractères',
        maxMessage: 'Le prénom doit faire au plus {{ limit }} caractères',
    )]
    #[Assert\NotBlank(message: 'Le prénom est obligatoire')]
    #[Assert\Type(type: 'string')]
    private string $firstname;

    #[ORM\Column(length: 50)]
    #[Assert\Length(
        min: 3,
        max: 50,
        minMessage: 'Le nom doit faire au moins {{ limit }} caractères',
        maxMessage: 'Le nom doit faire au plus {{ limit }} caractères',
    )]
    #[Assert\NotBlank(message: 'Le nom est obligatoire')]
    private string $lastname;

    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\Length(
        min: 3,
        max: 50,
        minMessage: 'Le pseudo doit comporter au moins {{ limit }} caractères.',
        maxMessage: 'Le pseudo ne peut pas comporter plus de {{ limit }} caractères.'
    )]
    private ?string $pseudo = null;

    #[ORM\Column(length: 10, nullable: true)]
    #[Assert\Type(type: 'numeric', message: 'Seuls les chiffres sont acceptés.')]
    #[Assert\Length(min: 10, max: 10, exactMessage: 'Le numéro de téléphone doit comporter 10 chiffres.')]
    private ?string $telephone = null;

    #[ORM\Column(length: 150, nullable: true)]
    #[Assert\Length(
        max: 150,
        maxMessage: 'L\'adresse ne peut pas comporter plus de {{ limit }} caractères.'
    )]
    #[Assert\Type(type: 'string', message: 'Seuls les caractères alphabétiques sont acceptés.')]
    private ?string $address;

    #[ORM\Column(length: 200, nullable: true)]
    #[Assert\Length(
        min: 4,
        max: 200,
        minMessage: 'Le complément d\'adresse doit comporter au moins {{ limit }} caractères.',
        maxMessage: ' Le complément d\'adresse ne peut pas comporter plus de {{ limit }} caractères.'
    )]
    #[Assert\Type(type: 'string', message: 'Seuls les caractères alphabétiques sont acceptés.')]
    private ?string $complementAddress = null;

    #[ORM\Column(length: 5, nullable: true)]
    #[Assert\Length(
        min: 5,
        max: 5,
        exactMessage: 'Le code postal doit comporter 5 chiffres.'
    )]
    private ?string $postalCode = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\Length(
        min: 3,
        max: 50,
        minMessage: 'La ville doit comporter au moins {{ limit }} caractères.',
        maxMessage: 'La ville ne peut pas comporter plus de {{ limit }} caractères.'
    )]
    #[Assert\Type(type: 'string', message: 'Seuls les caractères alphabétiques sont acceptés.')]
    private ?string $town = null;

    #[ORM\Column]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?DateTimeImmutable $updatedAt = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTime $birthday = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $function = null;

    #[ORM\Column(type: 'boolean')]
    private $isVerified = false;

    #[ORM\Column(nullable: true)]
    private ?DateTimeImmutable $agreedTermsAt = null;

    #[ORM\Column(nullable: true)]
    private ?DateTimeImmutable $askDeleteAccountAt = null;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }

    public function __toString(): string
    {
        $firstname = $this->firstname;
        $lastname = $this->lastname;

        $firstname = ucfirst($firstname);
        $lastname = ucfirst($lastname);

        return $firstname . ' ' . $lastname;
    }

    #[ORM\PreUpdate]
    public function preUpdate(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }


    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(?string $pseudo): self
    {
        $this->pseudo = $pseudo;

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

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getComplementAddress(): ?string
    {
        return $this->complementAddress;
    }

    public function setComplementAddress(?string $complementAddress): self
    {
        $this->complementAddress = $complementAddress;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(?string $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getTown(): ?string
    {
        return $this->town;
    }

    public function setTown(?string $town): self
    {
        $this->town = $town;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getBirthday(): ?DateTime
    {
        return $this->birthday;
    }

    public function setBirthday(?DateTime $birthday): self
    {
        $this->birthday = $birthday;

        return $this;
    }

    // Count age from birthday
    public function getAge(): int
    {
        $now = new DateTimeImmutable();

        return $now->diff($this->birthday)->y;
    }

    // Create function to know if today is birthday
    public function isBirthday(): bool
    {
        $now = new DateTimeImmutable();
        $birthday = $this->birthday->format('d-m');
        $today = $now->format('d-m');

        return $birthday === $today;
    }

    public function getFullName(): ?string
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    public function getFunction(): ?string
    {
        return $this->function;
    }

    public function setFunction(?string $function): self
    {
        $this->function = $function;

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getAgreedTermsAt(): ?DateTimeImmutable
    {
        return $this->agreedTermsAt;
    }

    public function setAgreedTermsAt(DateTimeImmutable $agreedTermsAt): self
    {
        $this->agreedTermsAt = $agreedTermsAt;

        return $this;
    }

    public function getAskDeleteAccountAt(): ?DateTimeImmutable
    {
        return $this->askDeleteAccountAt;
    }

    public function setAskDeleteAccountAt(?DateTimeImmutable $askDeleteAccountAt): self
    {
        $this->askDeleteAccountAt = $askDeleteAccountAt;

        return $this;
    }
}
