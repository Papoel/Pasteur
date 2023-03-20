<?php

declare(strict_types=1);

namespace App\Controller\Payment;

use App\Entity\Order\OrderDetails;
use App\Repository\Order\OrderRepository;
use App\Services\CartService;
use App\Services\MailService;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class StripeController extends AbstractController
{
    /** @throws ApiErrorException */
    #[Route('/ma-commande/paiement/create-session', name: 'app_stripe_payment_products')]
    public function create(CartService $cart): RedirectResponse
    {
        Stripe::setApiKey($this->getParameter(name: 'STRIPE_KEY_SECRET'));

        $fullCart = $cart->getFullCart();
        $customerEmail = $fullCart[array_key_first($fullCart)]['customerEmail'];

        $product_for_stripe = [];
        foreach ($cart->getFullCart() as $product) {
            $product_for_stripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $product['product']->getPrice(),
                    'product_data' => [
                        'name' => $product['product']->getName(),
                    ],
                ],
                'quantity' => $product['quantity'],
            ];
        }

        $orderId = $cart->getOrderId();
        // Add orderId to metadata
        $metadata = [
            'type' => 'order',
            'orderId' => $orderId,
        ];
        // Ajouter la commande à metadata
        $index = 1; // initialisation de l'index à 1
        foreach ($cart->getFullCart() as $product) {
            $metadata["product_id_$index"] = $product['product']->getId();
            $metadata["product_name_$index"] = $product['product']->getName();
            $metadata["product_price_$index"] = $product['product']->getPrice();
            $metadata["product_quantity_$index"] = $product['quantity'];
            $index++; // incrémentation de l'index pour chaque produit ajouté aux métadonnées
        }

        $charge_session = Session::create(params: [
            'customer_email' => $customerEmail,
            'mode' => 'payment',
            'payment_method_types' => ['card'],
            'line_items' => $product_for_stripe,
            'success_url' => $this->generateUrl(
                route: 'app_stripe_payment_products_confirmation',
                referenceType: UrlGeneratorInterface::ABSOLUTE_URL
            ),
            'cancel_url' => $this->generateUrl(
                route: 'app_stripe_payment_products_cancel',
                referenceType: UrlGeneratorInterface::ABSOLUTE_URL
            ),
            'metadata' => $metadata
        ]);
        return $this->redirect($charge_session->url, status: 303);
    }

    #[Route('/ma-commande/paiement/confirmation', name:'app_stripe_payment_products_confirmation')]
    public function paymentConfirmation(
        CartService $cartService,
        MailService $mailService,
        OrderRepository $orderRepository,
    ): Response {
        // Send message flash
        $this->addFlash(type: 'success', message: 'Votre paiement a bien été effectué.');

        $to = $cartService->getFullCart()[array_key_first($cartService->getFullCart())]['customerEmail'];
        $orderId = $cartService->getOrderId();
        $order = $orderRepository->findOneBy(['id' => $orderId]);
        $orderDetails = $order->getOrderDetails();
        $day = new \DateTime();

        $mailService->sendEmail(
            from: 'contact@aperp.info',
            to: $to,
            subject: 'Confirmation de votre commande',
            htmlTemplate: 'emails/confirm_order.html',
            context: [
                'commande' => $cartService->getFullCart(),
                'details' => $orderDetails,
                'client' => $order->getFullName(),
                'mail' => $order->getEmail(),
                'telephone' => $order->getTelephone(),
                'date' => $day->format(format: 'd/m/Y'),
                'paiement' => $order->getStripePaymentIntentId(),
                'quantityTotal' => $cartService->getQuantity(),
                'totalOrder' => $cartService->getTotal(),
                'facture' => $day->format(format: 'Ymd') . '-' . str_pad(
                    (string)$order->getId() ,
                    length: 4,
                    pad_string: '0',
                    pad_type: STR_PAD_LEFT
                ),
            ]
        );

        // remove cart from cartService and redirect to home
        $cartService->removeCart();

        return $this->redirectToRoute(route: 'app_home');
    }

    #[Route('/ma-commande/paiement/annulation', name:'app_stripe_payment_products_cancel')]
    public function paymentCancel(CartService $cartService): Response
    {
        // Send message flash
        $this->addFlash(type: 'danger', message: 'Nous avons supprimé votre panier, vous pouvez le remplir à nouveau.');

        // remove cart from cartService and redirect to home
        $cartService->deleteAllToCart();

        // return $this->render('payment/confirmation.html.twig');
        return $this->redirectToRoute(route: 'app_home');
    }
}
