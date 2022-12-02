<?php

namespace App\DataFixtures;

use App\Entity\Contact\Contact;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ContactFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create(locale: 'fr_FR');

        for ($i = 0; $i <= 5; ++$i) {
            $contact = new Contact();

            $contact->setFullName(fullName: $faker->name())
                ->setEmail(email: $faker->email())
                ->setSubject(subject: 'Demande n°'.$i + 1)
                ->setMessage(message: $faker->text())
            ;

            $manager->persist($contact);
        }

        $manager->flush();
    }
}
