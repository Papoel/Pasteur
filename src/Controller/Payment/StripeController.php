<?php

declare(strict_types=1);

namespace App\Controller\Payment;

use App\Services\CartService;
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
    public function paymentConfirmation(CartService $cartService): Response
    {
        // Send message flash
        $this->addFlash(type: 'success', message: 'Votre paiement a bien été effectué.');

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
