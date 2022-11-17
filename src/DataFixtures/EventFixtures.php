<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Event\Event;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class EventFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $events = [];

        $event = new Event();
        $event->setName(name: 'Apéro PHP');
        $event->setDescription(description: 'Apéro PHP est un événement mensuel qui a lieu à Maubeuge. Il est organisé par la communauté PHP francophone.');
        $event->setLocation(location: 'Maubeuge');
        $event->setPrice(price: 0);
        $event->setStartsAt(startsAt: new \DateTimeImmutable(datetime: '2023/06/14 11:00'));
        $event->setFinishAt(finishAt:  new \DateTimeImmutable(datetime: '2023/06/16 18:30'));
        $event->setStatus(status: 1);
        $event->setCapacity(capacity: 50);
        $event->setImageFileName(imageFileName: 'event.jpeg');
        $event->setCreatedAt(createdAt: new \DateTimeImmutable(datetime: 'now'));
        $event->setUpdatedAt(updatedAt: new \DateTimeImmutable(datetime: 'now + 1 day'));
        $event->setHelpNeeded(helpNeeded: true);
        // $event->setSlug($event->getName());

        $manager->persist($event);
        $events[] = $event;

        for ($newEvent = 1; $newEvent <= 10; ++$newEvent) {
            $event = new Event();
            $event->setName(name: $faker->sentence(nbWords: 3, variableNbWords: true));
            $event->setDescription(description: $faker->paragraph(nbSentences: 3, variableNbSentences: true));
            $event->setLocation(location: $faker->city());
            $event->setPrice(price: $faker->randomFloat(nbMaxDecimals: 2, min: 0, max: 50));
            $event->setStatus($faker->numberBetween(0, 2));
            $event->setCapacity(capacity: $faker->numberBetween(10, 100));
            $event->setImageFileName(imageFileName: 'event.jpeg');
            $event->setHelpNeeded(helpNeeded: $faker->boolean());

            $date = $faker->dateTimeBetween(startDate: 'now', endDate: '+6 months');
            $immutable = \DateTimeImmutable::createFromMutable($date);
            $event->setStartsAt($immutable);

            $date = $event->getStartsAt();

            $date = $date->modify(modifier: '+'. $faker->numberBetween(int1: 4, int2: 16) .' hours');
            $event->setFinishAt($date);

            $date = $faker->dateTimeBetween(startDate: '-3 months', endDate: '-1 day');
            $immutable = \DateTimeImmutable::createFromMutable($date);
            $event->setCreatedAt($immutable);

            $date = $faker->dateTimeBetween(startDate: '-3 months', endDate: '-1 day');
            $immutable = \DateTimeImmutable::createFromMutable($date);
            $event->setUpdatedAt($immutable);

            // $event->setSlug($event->getName());

            $manager->persist($event);
            $events[] = $event;
        }

        $manager->flush();
    }
}
