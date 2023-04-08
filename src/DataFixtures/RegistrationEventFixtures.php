<?php

namespace App\DataFixtures;

use App\Entity\Event\RegistrationEvent;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class RegistrationEventFixtures extends Fixture implements dependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        for ($i = 0; $i < 30; $i++) {
            $registrationEvent = new RegistrationEvent();
            $registrationEvent->setEvent(event: $this->getReference(name: 'event_' . rand(1, 10)));
            $registrationEvent->setFirstname(firstname: $faker->firstName);
            $registrationEvent->setLastname(lastname: $faker->lastName);
            $registrationEvent->setEmail(email: $faker->email);
            $registrationEvent->setTelephone(telephone: $faker->numerify('06########'));
            // 60% des inscriptions sont payées
            if (random_int(1, 10) <= 6) {
                $registrationEvent->setPaid(Paid: true);
            }

            // add reference for children fixtures
            $this->addReference(name: 'registration_event_' . $i, object: $registrationEvent);

            // update the event Capacity and registered
            $event = $registrationEvent->getEvent();
            $event->setCapacity(capacity: $event->getCapacity() - 1);
            $event->setRegistered(registered: $event->getRegistered() + 1);
            $manager->persist($registrationEvent);
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
