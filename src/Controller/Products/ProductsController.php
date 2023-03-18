<?php

declare(strict_types=1);

namespace App\Controller\Products;

use App\Entity\Product\Product;
use App\Repository\Product\ProductRepository;
use App\Services\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductsController extends AbstractController
{
    #[Route('/produits', name: 'app_products')]
    public function index(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findUpComing();
        return $this->render(view: 'products/index.html.twig', parameters: [
            'products' => $products,
        ]);
    }

    #[Route('/produit/{slug}', name: 'app_product_show')]
    public function show(Product $product, ProductRepository $productRepository): Response
    {
        $products = $productRepository->findUpComing();

        return $this->render(view: 'products/show.html.twig', parameters: [
            'product' => $product,
            'products' => $products,
        ]);
    }
}
