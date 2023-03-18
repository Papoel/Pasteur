<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Order\Order;
use App\Services\CartService;
use Doctrine\Common\EventSubscriber;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class OrderSubscriber implements EventSubscriber
{
    public function __construct(private cartService $cartService)
    {
    }

    public function getSubscribedEvents()
    {
        return [
            'postPersist',
        ];
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if ($entity instanceof Order) {
            $this->cartService->setOrderId($entity->getId());
        }
    }

}
