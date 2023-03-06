<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Slot\Slot;
use App\Repository\Event\EventRepository;
use App\Repository\Product\ProductRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SlotFixtures extends Fixture implements DependentFixtureInterface
{

    public function __construct(
        private readonly EventRepository $eventRepository,
        private readonly ProductRepository $productRepository
    ) {
    }

    public function load(ObjectManager $manager)
    {
        $slots = [];
        $slot = new Slot();

        // For each hour from 08:00 to 20:00
        for ($hour = 8; $hour <= 20; ++$hour) {
            $slot = new Slot();
            $slot->setStartsAt(startsAt: new \DateTime(datetime: $hour . ':00:00'));
            $slot->setEndsAt(endsAt: new \DateTime(datetime: ($hour + 1) . ':00:00'));

            $manager->persist($slot);
            $slots[] = $slot;
        }

        $events = $this->eventRepository->findAll();
        $products = $this->productRepository->findAll();

        foreach ($slots as $slot) {
            $nbEvents = random_int(min: 1, max: 3);
            for ($i = 0; $i < $nbEvents; ++$i) {
                $event = $events[random_int(min: 0, max: count($events) - 1)];
                $product = $products[random_int(min: 0, max: count($products) - 1)];
                $slot->addEvent(event: $event);
                $slot->addProduct(product: $product);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            EventFixtures::class,
        ];
    }
}
