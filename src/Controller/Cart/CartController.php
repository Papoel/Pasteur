<?php

namespace App\Controller\Cart;

use App\Services\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('mon-panier', name: 'app_cart_')]
class CartController extends AbstractController
{
    #[Route(name: 'index')]
    public function index(CartService $cartService): Response
    {
//        dd($cartService->getFullCart());
        return $this->render(view: 'cart/index.html.twig', parameters: [
            'cart' => $cartService->getFullCart() ,
            'total' => $cartService->getTotal(),
            'quantity' => $cartService->getQuantity(),
        ]);
    }

    #[Route('/ajouter/{id}', name: 'add')]
    public function addToCart(CartService $cartService, int $id): Response
    {
        $cartService->addToCart($id);
        $errorMessage = $cartService->getErrorMessage();
        if ($errorMessage) {
            $this->addFlash(type: 'danger', message: $errorMessage);
        }

        return $this->redirectToRoute(route: 'app_cart_index');
    }

    #[Route('/supprimer/{id}', name: 'remove')]
    public function removeToCart(CartService $cartService, int $id): Response
    {
        $cartService->deleteProductToCart($id);

        return $this->redirectToRoute(route: 'app_cart_index');
    }

    #[Route('/tout-supprimer', name: 'remove-all')]
    public function removeAll(CartService $cartService): Response
    {
        $cartService->deleteAllToCart();

        return $this->redirectToRoute(route: 'app_products');
    }

    #[Route('/decrementer/{id}', name: 'decrement')]
    public function decrease(CartService $cartService, int $id): RedirectResponse
    {
        $cartService->decreaseFromCart($id);

        return $this->redirectToRoute(route: 'app_cart_index');
    }
}
