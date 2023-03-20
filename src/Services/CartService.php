<?php

namespace App\Services;

use App\Entity\Order\Order;
use App\Entity\Order\OrderDetails;
use App\Entity\Product\Product;
use App\Repository\Order\OrderDetailsRepository;
use App\Repository\Order\OrderRepository;
use App\Repository\Product\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;


class CartService
{
    private string $errorMessage;

    public function __construct(
        protected RequestStack $requestStack,
        private EntityManagerInterface $entityManager,
        private ProductRepository $productRepository,
        private OrderDetailsRepository $orderDetailsRepository,
        private OrderRepository $orderRepository,
        private Security $security,
    ) { }

    public function getCart(): array
    {
        return $this->requestStack->getSession()->get(name: 'cart', default: []);
    }
    public function updateCart($cart): void
    {
        // Mettre à jour le panier dans la session
        $this->requestStack->getSession()->set('cart', $cart);
        $this->requestStack->getSession()->set('cartData', $this->getFullCart());
    }
    public function addToCart(int $id): void
    {
        // Récupérer le panier dans la session
        $cart = $this->getCart();

        // Vérifier si le produit est en stock
        /** @var Product $product */
        $product = $this->productRepository->find($id);
        $stock = $product->getStock();

        if ($stock <= 0) {
            $this->errorMessage = 'Le produit n\'est plus en stock';
            return;
        }

        // Si le produit existe déjà dans le panier
        if (!empty($cart[$id])) {
            // On vérifie si la quantité demandée peut être ajoutée au panier
            if ($cart[$id] < $stock) {
                // On incrémente la quantité
                $cart[$id]++;
            } else {
                $this->errorMessage = 'Désolé, la limite de disponibilité est atteinte pour ce produit';
                return;
            }
        } else {
            // Sinon, on ajoute le produit au panier
            $cart[$id] = 1;
        }
        $this->updateCart($cart);
    }



    // Ajouter un produit au panier
    public function ExaddToCart(int $id): void
    {
        // Récupérer le panier dans la session
        $cart = $this->getCart();

        // Vérifier si le produit est en stock
        $product = $this->productRepository->find($id);
        if ($product->getStock() <= 0) {
            $this->errorMessage = 'Le produit n\'est plus en stock';
            return;
        }

        // Si le produit existe déjà dans le panier
        if (!empty($cart[$id])) {
            // On incrémente la quantité
            $cart[$id]++;
        } else {
            // Sinon, on ajoute le produit au panier
            $cart[$id] = 1;
        }

        // Mettre à jour les entités liées au panier
        // $this->decreaseProductStock($id);
        // $this->increaseProductReserved($id);

        // Mettre à jour le panier dans la session
        $this->updateCart($cart);
    }
    // Supprimer une unité de produit du panier
    public function decreaseFromCart(int $id): void
    {
        $cart = $this->getCart();

        // Vérifier si le produit est déjà dans le panier
        if (isset($cart[$id])) {
            if ($cart[$id] > 1) {
                // Produit dans le panier
                // 1. Je décrémente la quantité
                $cart[$id]--;
            } else {
                // Produit dans le panier avec une quantité égal à 1
                unset($cart[$id]);
            }
        }

        // Mettre à jour les entités liées au panier
//         $this->decreaseProductReserved($id);
//         $this->increaseProductStock($id);

        // Mise à jour du panier
        $this->updateCart($cart);
    }
    // Supprimer totalement le produit du panier
    public function deleteProductToCart(int $id): void
    {
        $cart = $this->getCart();

        // Calculer le nombre d'articles commandés
        $reserved = $cart[$id];

        // Ajouter le nombre d'articles commandés au stock du produit
//         $this->increaseProductStock($id, $reserved);

        // Retirer le nombre d'articles commandés au total d'article commandé
//         $this->decreaseProductReserved($id, $reserved);

        // Vérifier si le produit est déjà dans le panier
        if (isset($cart[$id])) {
            // Produit dans le panier
            // 1. Je supprime le produit
            unset($cart[$id]);
        }

        // Mise à jour du panier
        $this->updateCart($cart);
    }
    public function deleteAllToCart(): void
    {
        // Récupérer le panier dans la session

        // Mettre à jour les entités liées au panier
//         $this->updateStockAndReservedWhenCartIsEmpty();

        // Vider le panier dans la session
        $this->removeOrder();
        $this->requestStack->getSession()->remove(name: 'cart');
        $this->requestStack->getSession()->remove(name: 'cartData');
        $this->requestStack->getSession()->remove(name: 'orderId');

        // Enregistrer les modifications en base de données
        $this->entityManager->flush();
    }
    // Récupérer le panier complet
    public function getFullCart(): array
    {
        $fullCart = [];
        $cart = $this->getCart();
        $products = $this->productRepository->findAll();

        // Récupérer l'utilisateur connecté
        $user = $this->security->getUser();

        // Récupérer l'adresse email associée à la commande si aucun utilisateur n'est connecté
        $order = $this->orderRepository->findOneBy(['id' => $this->getOrderId()]);
        if ($order !== null) {
            $orderEmail = $order->getEmail();
        } else {
            $orderEmail = null;
        }

        // Utiliser l'opérateur ternaire pour récupérer l'adresse email
        $customerEmail = $user !== null ? $user->getEmail() : $orderEmail;

        foreach ($products as $product) {
            if (isset($cart[$product->getId()])) {
                $fullCart[$product->getId()] = [
                    'productId' => $product->getId(),
                    'product'   => $product,
                    'quantity'  => $cart[$product->getId()],
                    'price'     => $product->getPrice(),
                    'total'     => $product->getPrice() * $cart[$product->getId()],
                    'stock'     => $product->getStock(),
                    'orderId'   => $this->getOrderId(),
                    'customerEmail' => $customerEmail,
                    'stockAvailableAfterCommand' => $product->getStock() - $cart[$product->getId()],
                ];
            }
        }
        return $fullCart;
    }
    // Récupérer le prix total de tous les produits du panier
    public function getTotal(): float
    {
        $total = 0;
        $fullCart = $this->getFullCart();
        foreach ($fullCart as $item) {
            $total += $item['total'];
        }
        return $total;
    }
    // Calculer la quantité de produits dans le panier
    public function getQuantity(): int
    {
        $quantity = 0;
        $fullCart = $this->getFullCart();
        foreach ($fullCart as $item) {
            $quantity += $item['quantity'];
        }
        return $quantity;
    }

    /************ Méthode lié à Order ************/
    public function setOrderId(?int $orderId): void
    {
        $this->orderId = $orderId;
        $this->requestStack->getSession()->set(name: 'orderId', value: $orderId);
    }
    public function getOrderId()
    {
        return $this->requestStack->getSession()->get(name: 'orderId');
    }
    public function removeOrderId(): void
    {
        $this->requestStack->getSession()->remove(name: 'orderId');
    }
    public function removeOrder(): void
    {
        $orderId = $this->getOrderId();

        if ($orderId !== null) {
            $orderDetails = $this->orderDetailsRepository->findBy(['myOrder' => $orderId]);
            $order = $this->orderRepository->find($orderId);

            foreach ($orderDetails as $orderDetail) {
                $this->entityManager->remove($orderDetail);
            }

            if ($order !== null) {
                $this->entityManager->remove($order);
            }

            $this->entityManager->flush();
        }

        $this->removeOrderId();

        // Supprimer les entrées correspondantes dans les tables order et order_details
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder
            ->delete(delete: Order::class , alias: 'o')
            ->where(predicates: 'o.id = :orderId')
            ->setParameter(key: 'orderId', value: $orderId)
            ->getQuery()
            ->execute();

        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder
            ->delete(delete: OrderDetails::class , alias: 'od')
            ->where(predicates: 'od.myOrder = :orderId')
            ->setParameter(key: 'orderId', value: $orderId)
            ->getQuery()
            ->execute();
    }
    public function removeCart(): void
    {
        $this->requestStack->getSession()->remove(name: 'cart');
        $this->requestStack->getSession()->remove(name: 'cartData');
        $this->removeOrderId();
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage ?? '';
    }

    /************ Méthode lié à la base de données ************/
    // Ajouter 1 unité au stock du produit dans la base de données
    public function increaseProductStock($productId, $quantity = 1): void
    {
        $product = $this->productRepository->find($productId);
        $product->setStock(stock: $product->getStock() + $quantity);
        $this->entityManager->persist($product);
        $this->entityManager->flush();
    }
    // Retirer 1 unité du stock du produit dans la base de données
    public function decreaseProductStock($productId, $quantity = 1): void
    {
        $product = $this->productRepository->find($productId);
        $product->setStock(stock: $product->getStock() - $quantity);
        $this->entityManager->persist($product);
        $this->entityManager->flush();
    }
    // Ajouter 1 unité au total d'article commandé dans la base de données
    public function increaseProductReserved($productId, $quantity = 1): void
    {
        $product = $this->productRepository->find($productId);
        $product->setReserved(reserved: $product->getReserved() + $quantity);
        $this->entityManager->persist($product);
        $this->entityManager->flush();
    }
    // Retirer 1 unité au total d'article commandé dans la base de données
    public function decreaseProductReserved($productId, $quantity = 1): void
    {
        $product = $this->productRepository->find($productId);
        $product->setReserved(reserved: $product->getReserved() - $quantity);
        $this->entityManager->persist($product);
        $this->entityManager->flush();
    }
    // Mettre à jour la base de données à la suppression du panier
    public function updateStockAndReservedWhenCartIsEmpty(): void
    {
        if (!empty($this->getCart())) {
            foreach ($this->getCart() as $productId => $quantity) {
                $this->increaseProductStock($productId, $quantity);
                $this->decreaseProductReserved($productId, $quantity);
            }
        }
    }
}
