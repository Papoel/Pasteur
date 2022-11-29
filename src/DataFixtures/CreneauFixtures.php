<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Creneau\Creneau;
use App\Repository\Event\EventRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CreneauFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly EventRepository $eventRepository,
    ) {
    }

    public function load(ObjectManager $manager)
    {
        $creneaux = [];
        $creneau = new Creneau();

        // For each hour from 08:00 to 20:00
        for ($hour = 8; $hour <= 20; ++$hour) {
            $creneau = new Creneau();
            $creneau->setStartsAt(startsAt: new \DateTime(datetime: $hour.':00:00'));
            $creneau->setEndsAt(endsAt: new \DateTime(datetime: ($hour + 1).':00:00'));

            $manager->persist($creneau);
            $creneaux[] = $creneau;
        }

        $events = $this->eventRepository->findAll();

        foreach ($creneaux as $creneau) {
            $nbEvents = random_int(min: 1, max: 3);
            for ($i = 0; $i < $nbEvents; ++$i) {
                $event = $events[random_int(min: 0, max: count($events) - 1)];
                $creneau->addEvent(event: $event);
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
