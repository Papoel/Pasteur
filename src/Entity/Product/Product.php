<?php

namespace App\Entity\Product;

use App\Entity\Order\OrderDetails;
use App\Entity\Slot\Slot;
use App\Entity\Payment;
use App\Entity\Event\RegistrationHelp;
use Cocur\Slugify\Slugify;
use App\Repository\Product\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation\Uploadable;
use Vich\UploaderBundle\Mapping\Annotation\UploadableField;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[Uploadable]
#[UniqueEntity(
    fields: ['slug'],
    message: 'Le produit {{ value }} existe déjà en base de données.'
)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

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

    #[ORM\Column(length: 100, nullable: true)]
    #[Assert\Length(
        max: 100,
        maxMessage: 'Le lieu ne peut pas comporter plus de {{ limit }} caractères.'
    )]
    private ?string $location;

    #[ORM\Column(type: Types::INTEGER, precision: 4, scale: 2, nullable: true)]
    #[Assert\GreaterThanOrEqual(0)]
    #[Assert\LessThan(
        10000,
        message: 'Le prix ne doit pas dépasser {{ value }} €'
    )]
    private ?int $price = null;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Assert\NotBlank]
    private \DateTimeImmutable $startsAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    #[Assert\GreaterThan(
        propertyPath: 'startsAt',
        message: 'La date de fin doit être supérieur à la dae de début.'
    )]
    private ?\DateTimeImmutable $finishAt = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    #[Assert\PositiveOrZero]
    private ?int $stock = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(nullable: true)]
    private ?bool $helpNeeded = null;

    #[ORM\Column]
    private bool $published = false;

    #[UploadableField(mapping: 'product_image', fileNameProperty: 'imageName')]
    private ?File $imageFile = null;

    #[ORM\Column(type: 'string', nullable: true, options: ['default' => 'event.jpeg'])]
    private ?string $imageName = 'event.jpeg';

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $deliveryAt = null;

    #[ORM\ManyToMany(targetEntity: Slot::class, inversedBy: 'products')]
    #[ORM\JoinTable(name: 'slot_product')]
    private Collection $creneaux;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: RegistrationHelp::class)]
    private Collection $registrationHelp;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: Payment::class)]
    private Collection $payments;

    #[ORM\Column(nullable: true)]
    private ?int $reserved = null;

    #[ORM\Column(type: 'boolean')]
    private bool $DeliveredSchool = false;

    #[ORM\OneToMany(mappedBy: 'productId', targetEntity: OrderDetails::class)]
    private Collection $orderDetails;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();

        $this->creneaux = new ArrayCollection();
        $this->registrationHelp = new ArrayCollection();
        $this->payments = new ArrayCollection();
        $this->orderDetails = new ArrayCollection();
    }

    #[ORM\PrePersist]
    public function prePersist(): void
    {
        $this->slug = (new Slugify())->slugify($this->name);

        if ($this->isDeliveredSchool() === true) {
            $this->setLocation(location: 'École');
        }
    }

    public function isFree(): bool
    {
        return (0 == $this->getPrice()) || is_null($this->getPrice());
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

    public function getDescription(): string
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

    public function setLocation(?string $location): self
    {
        $this->location = $location;

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

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): self
    {
        $this->stock = $stock;

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

    public function getDeliveryAt(): ?\DateTimeImmutable
    {
        return $this->deliveryAt;
    }

    public function setDeliveryAt(\DateTimeImmutable $deliveryAt): self
    {
        $this->deliveryAt = $deliveryAt;

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
     * @return Collection<int, RegistrationHelp>
     */
    public function getRegistrationHelp(): Collection
    {
        return $this->registrationHelp;
    }

    public function addRegistrationHelp(RegistrationHelp $registrationHelp): self
    {
        if (!$this->registrationHelp->contains($registrationHelp)) {
            $this->registrationHelp->add($registrationHelp);
            $registrationHelp->setProduct($this);
        }

        return $this;
    }

    public function removeRegistrationHelp(RegistrationHelp $registrationHelp): self
    {
        if ($this->registrationHelp->removeElement($registrationHelp)) {
            // set the owning side to null (unless already changed)
            if ($registrationHelp->getProduct() === $this) {
                $registrationHelp->setProduct(null);
            }
        }

        return $this;
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
            $payment->setProduct($this);
        }

        return $this;
    }

    public function removePayment(Payment $payment): self
    {
        if ($this->payments->removeElement($payment)) {
            // set the owning side to null (unless already changed)
            if ($payment->getProduct() === $this) {
                $payment->setProduct(null);
            }
        }

        return $this;
    }

    public function getReserved(): ?int
    {
        return $this->reserved;
    }

    public function setReserved(?int $reserved): self
    {
        $this->reserved = $reserved;

        return $this;
    }

    public function isDeliveredSchool(): bool
    {
        return $this->DeliveredSchool;
    }


    public function setDeliveredSchool(bool $DeliveredSchool): self
    {
        $this->DeliveredSchool = $DeliveredSchool;

        return $this;
    }

    /**
     * @return Collection<int, OrderDetails>
     */
    public function getOrderDetails(): Collection
    {
        return $this->orderDetails;
    }

    public function addOrderDetail(OrderDetails $orderDetail): self
    {
        if (!$this->orderDetails->contains($orderDetail)) {
            $this->orderDetails->add($orderDetail);
            $orderDetail->setProductId($this);
        }

        return $this;
    }

    public function removeOrderDetail(OrderDetails $orderDetail): self
    {
        if ($this->orderDetails->removeElement($orderDetail)) {
            // set the owning side to null (unless already changed)
            if ($orderDetail->getProductId() === $this) {
                $orderDetail->setProductId(null);
            }
        }

        return $this;
    }
}
