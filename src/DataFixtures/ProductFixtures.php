<?php

namespace App\DataFixtures;

use App\Entity\Event\Event;
use App\Entity\Product\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create(locale: 'fr_FR');

        $products = [];
        // ################################################ RANDOM ################################################ //
        for ($newProduct = 1; $newProduct <= 10; ++$newProduct) {
            $product = new Product();
            $product->setName(name: $faker->sentence(nbWords: 3));
            $product->setDescription(description: $faker->paragraph(3));
            $product->setLocation(location: $faker->boolean(chanceOfGettingTrue: 55) ? null : $faker->city());
            $product->setPrice(price: $faker->numberBetween(int1: 0, int2: 9999));

            $date = $faker->dateTimeBetween(startDate: 'now', endDate: '+6 months');
            $immutable = \DateTimeImmutable::createFromMutable($date);
            $product->setStartsAt($immutable);

            $date = $product->getStartsAt();
            $date = $date->modify(modifier: '+' . $faker->numberBetween(int1: 4, int2: 16) . ' hours');
            $product->setFinishAt($date);

            $product->setCapacity(capacity: $faker->numberBetween(int1: 5, int2: 350));
            $product->setHelpNeeded(helpNeeded: $faker->boolean(chanceOfGettingTrue: 25));
            $product->setPublished(published: $faker->boolean(chanceOfGettingTrue: 75));
            $product->setImageName(imageName: 'event.jpeg');

            $date = $faker->dateTimeBetween(startDate: '-3 months', endDate: '-1 day');
            $immutable = \DateTimeImmutable::createFromMutable($date);
            $product->setCreatedAt($immutable);

            $date = $faker->dateTimeBetween(startDate: '-3 months', endDate: '-1 day');
            $immutable = \DateTimeImmutable::createFromMutable($date);
            $product->setUpdatedAt($immutable);

            $product->setRegistered(registered: $faker->numberBetween(int1: 0, int2: $product->getCapacity()));

            $manager->persist($product);
            $products[] = $product;
        }

        $manager->flush();
    }
}
