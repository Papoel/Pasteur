<?php

namespace App\Entity\Contact;

use App\Repository\ContactRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ContactRepository::class)]
class Contact
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, nullable: true)]
    #[Assert\Length(
        min: 3,
        max: 50,
        minMessage: 'Le nom doit faire au moins {{ limit }} caractères',
        maxMessage: 'Le nom doit faire au maximum {{ limit }} caractères'
    )]
    private ?string $fullName = null;

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank(message: 'L\'email est obligatoire')]
    #[Assert\Email(
        message: 'L\'adresse email "{{ value }}" n\'est pas valide',
    )]
    #[Assert\Length(
        min: 6,
        max: 180,
        maxMessage: 'L\'adresse email doit faire au maximum {{ limit }} caractères'
    )]
    private string $email;

    #[ORM\Column(length: 100, nullable: true)]
    #[Assert\Length(
        min: 3,
        max: 100,
        minMessage: 'Le sujet doit faire au moins {{ limit }} caractères',
        maxMessage: 'Le sujet doit faire au maximum {{ limit }} caractères'
    )]
    private ?string $subject = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'Le message ne peut pas être vide')]
    #[Assert\Length(
        min: 5,
        minMessage: 'Le message doit faire au moins {{ limit }} caractères'
    )]
    private string $message;

    #[ORM\Column]
    #[Assert\NotNull]
    private ?\DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    private ?bool $isReplied = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $response = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $replyAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(?string $fullName): self
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(?string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

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

    public function getIsReplied(): ?bool
    {
        return $this->isReplied;
    }

    public function setIsReplied(?bool $isReplied): self
    {
        $this->isReplied = $isReplied;

        return $this;
    }

    public function getResponse(): ?string
    {
        return $this->response;
    }

    public function setResponse(?string $response): self
    {
        $this->response = $response;

        return $this;
    }

    public function getReplyAt(): ?\DateTimeImmutable
    {
        return $this->replyAt;
    }

    public function setReplyAt(?\DateTimeImmutable $replyAt): self
    {
        $this->replyAt = $replyAt;

        return $this;
    }
}
