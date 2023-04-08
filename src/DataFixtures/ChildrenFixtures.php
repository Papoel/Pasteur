<?php

namespace App\DataFixtures;

use App\Entity\Event\Children;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ChildrenFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        // Create between 1 and 6 childrens for each registration_event
        for ($i = 0; $i < 30; $i++) {
            $nbChildrens = random_int(1, 6);
            for ($j = 0; $j < $nbChildrens; $j++) {
                $children = new Children();
                $children->setRegistrationEvent(registrationEvent: $this->getReference(name: 'registration_event_' . $i));
                $children->setFirstname(firstname: $faker->firstName);
                $children->setLastname(lastname: $faker->lastName);
                $classroom = [
                    'CP',
                    'CE1',
                    'CE2',
                    'CM1',
                    'CM2',
                    'Extérieur'
                ];
                $children->setClassroom(classroom: $classroom[array_rand(array: $classroom)]);
                $manager->persist($children);
            }
        }


        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            RegistrationEventFixtures::class,
        ];
    }
}
