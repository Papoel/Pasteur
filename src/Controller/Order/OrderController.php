<?php

namespace App\Controller\Order;

use App\Entity\Order\Order;
use App\Entity\Order\OrderDetails;
use App\Entity\User\User;
use App\Form\OrderFormType;
use App\Services\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) { }

    #[Route('/ma-commande', name: 'app_order', methods: ['GET', 'POST'])]
    public function index(CartService $cart, Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if ($user) {
            if (!$cart->getOrderId()) {
                // Créer un nouvel Order
                $order = new Order();
                $order->setUser($user);
                $order->setFirstname(firstname: $user->getFirstname());
                $order->setLastname(lastname: $user->getLastname());
                $order->setTelephone(telephone: $user->getTelephone());
                $order->setEmail(email: $user->getEmail());

                $this->entityManager->persist($order);

                // Enregistrer les produits de la commande en BDD (OrderDetails)
                foreach ($cart->getFullCart() as $product) {
                    $orderDetails = new OrderDetails();
                    $orderDetails->setMyOrder(myOrder: $order);
                    $orderDetails->setProduct(product: $product['product']->getName());
                    $orderDetails->setQuantity(quantity: $product['quantity']);
                    $orderDetails->setPrice(price: $product['product']->getPrice());
                    $orderDetails->setTotal(total: $product['product']->getPrice() * $product['quantity']);
                    $orderDetails->setProductId(productId: $product['product']);

                    $this->entityManager->persist($orderDetails);
                }

                $this->entityManager->flush();

                /** @var Order $order */
                if ($order->getId()) {
                    $cart->setOrderId(orderId: $order->getId());
                }
            }

        } else {
            // Créer un formulaire pour les utilisateurs non connectés
            $form = $this->createForm(type: OrderFormType::class);
            $form->handleRequest(request: $request);

            // Vérifier si le formulaire est soumis et valide
            if ($form->isSubmitted() && $form->isValid()) {
                if (!$cart->getOrderId()) {
                    // Créer un nouvel Order
                    $order = new Order();
                    $order->setFirstname(firstname: $form->get('firstname')->getData());
                    $order->setLastname(lastname: $form->get('lastname')->getData());
                    $order->setTelephone(telephone: $form->get('telephone')->getData());
                    $order->setEmail(email: $form->get('email')->getData());

                    $this->entityManager->persist($order);

                    // Enregistrer les produits de la commande en BDD (OrderDetails)
                    foreach ($cart->getFullCart() as $product) {
                        $orderDetails = new OrderDetails();
                        $orderDetails->setMyOrder(myOrder: $order);
                        $orderDetails->setProduct(product: $product['product']->getName());
                        $orderDetails->setQuantity(quantity: $product['quantity']);
                        $orderDetails->setPrice(price: $product['product']->getPrice());
                        $orderDetails->setTotal(total: $product['product']->getPrice() * $product['quantity']);
                        $orderDetails->setProductId(productId: $product['product']);

                        $this->entityManager->persist($orderDetails);
                    }

                    // Enregistrer la commande en BDD (Order)
                    $this->entityManager->flush();

                    if ($order->getId()) {
                        $cart->setOrderId(orderId: $order->getId());
                    }
                } else {
                    // Mettre à jour les informations de l'utilisateur si elles ont changé
                    $order = $this->entityManager->getRepository(Order::class)->find($cart->getOrderId());
                    $order->setFirstname(firstname: $form->get('firstname')->getData());
                    $order->setLastname(lastname: $form->get('lastname')->getData());
                    $order->setTelephone(telephone: $form->get('telephone')->getData());
                    $order->setEmail(email: $form->get('email')->getData());

                    $this->entityManager->persist($order);
                    $this->entityManager->flush();

                    // Mettre à jour les produits de la commande en BDD (OrderDetails) si elles ont changé
                    foreach ($cart->getFullCart() as $product) {
                        $orderDetails = $this->entityManager->getRepository(OrderDetails::class)->findOneBy([
                            'myOrder' => $order,
                            'productId' => $product['product']
                        ]);

                        if ($orderDetails) {
                            $orderDetails->setQuantity(quantity: $product['quantity']);
                            $orderDetails->setTotal(total: $product['product']->getPrice() * $product['quantity']);
                        } else {
                            $orderDetails = new OrderDetails();
                            $orderDetails->setMyOrder(myOrder: $order);
                            $orderDetails->setProduct(product: $product['product']->getName());
                            $orderDetails->setQuantity(quantity: $product['quantity']);
                            $orderDetails->setPrice(price: $product['product']->getPrice());
                            $orderDetails->setTotal(total: $product['product']->getPrice() * $product['quantity']);
                            $orderDetails->setProductId(productId: $product['product']);
                        }

                        $this->entityManager->persist($orderDetails);
                    }
                    $this->entityManager->flush();
                }

                return $this->redirectToRoute(route: 'app_stripe_payment_products');
            }
        }

        // Envoyer un email de confirmation de commande

        // Définir manuellement des frais de service et de livraison
        $chargeService = 0;
        $chargeDelivery = 0;

        if ($cart->getOrderId()) {
            $order = $this->entityManager->getRepository(Order::class)->find($cart->getOrderId());
        }

        return $this->render(view: 'order/index.html.twig', parameters: [
            'form' => isset($form) ? $form->createView() : null,
            'cart' => $cart->getFullCart(),
            // Get order ID from orderId in session
            'order' => isset($order) ? $order : null,
            'chargeService' => $chargeService,
            'chargeDelivery' => $chargeDelivery
        ]);
    }

}
