<?php

namespace App\Entity\Order;

use App\Entity\Product\Product;
use App\Repository\Order\OrderDetailsRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: OrderDetailsRepository::class)]
class OrderDetails
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'orderDetails')]
    #[ORM\JoinColumn(nullable: false)]
    private Order $myOrder;

    #[ORM\Column(length: 150)]
    #[Assert\NotBlank(message: 'Le produit doit être renseigné')]
    private string $product;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'La quantité doit être renseigné')]
    #[Assert\Positive(message: 'La quantité doit être supérieure à 0')]
    private int $quantity;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Le prix doit être renseigné')]
    #[Assert\Positive(message: 'Le prix doit être supérieure à 0')]
    private int $price;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'Le total doit être renseigné')]
    #[Assert\Positive(message: 'Le total doit être supérieure à 0')]
    private int $total;

    #[ORM\ManyToOne(inversedBy: 'orderDetails')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $productId = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMyOrder(): ?Order
    {
        return $this->myOrder;
    }

    public function setMyOrder(?Order $myOrder): self
    {
        $this->myOrder = $myOrder;

        return $this;
    }

    public function getProduct(): ?string
    {
        return $this->product;
    }

    public function setProduct(string $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getTotal(): ?int
    {
        return $this->total;
    }

    public function setTotal(int $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function getProductId(): ?Product
    {
        return $this->productId;
    }

    public function setProductId(?Product $productId): self
    {
        $this->productId = $productId;

        return $this;
    }
}
