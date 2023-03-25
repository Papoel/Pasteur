<?php

declare(strict_types=1);

namespace App\Controller\Admin\Order;

use App\Entity\Order\Order;
use App\Entity\Order\OrderDetails;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AdminOrderController extends AbstractController
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    #[Route('/admin/order', name: 'app_admin_orders')]
    #[isGranted('ROLE_ADMIN')]
    public function index(): Response
    {
        // Vérifier que l'utilisateur est bien un admin
        $this->denyAccessUnlessGranted(
            attribute: 'ROLE_ADMIN',
            subject: "Accès à la section d'administration",
            message: 'Désolé, votre rôle ne vous donne pas accès a cette section.'
        );

        $orders = $this->em->getRepository(Order::class)->findAll();
        return $this->render(view: 'admin/order/index.html.twig', parameters: compact(var_name: 'orders'));
    }

    #[Route('/admin/order/{id}', name: 'app_admin_details_order')]
    #[isGranted('ROLE_ADMIN')]
    public function show(Order $order, OrderDetails $details): Response
    {
        // Vérifier que l'utilisateur est bien un admin
        $this->denyAccessUnlessGranted(
            attribute: 'ROLE_ADMIN',
            subject: "Accès à la section d'administration",
            message: 'Désolé, votre rôle ne vous donne pas accès a cette section.'
        );

        $orderDetails = $this->em->getRepository(OrderDetails::class)->findBy(['myOrder' => $order]);

        return $this->render(view: 'admin/order/show.html.twig', parameters: [
            'order' => $order,
            'details' => $orderDetails
        ]);
    }
}
