<?php

namespace App\DataFixtures;

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
        // ########################################## BOX FETE DES MERES ########################################## //
        $product = new Product();
        $product->setName(name: 'Box Fête des Mères');
        $product->setDescription(description: 'La box Fête des Mères est composée de 3 produits : un bouquet de fleurs, un parfum et un bijou.');
        $product->setLocation(location: 'Ecole');
        $product->setPrice(price: 1000);
        $product->setStartsAt(startsAt: new \DateTimeImmutable(datetime: '2023-05-22 08:00:00'));
        $product->setFinishAt(finishAt: new \DateTimeImmutable(datetime: '2023-05-31 16:30:00'));
        $product->setDeliveryAt(deliveryAt: new \DateTimeImmutable(datetime: '2023-06-02 16:30:00'));
        $product->setStock(stock: 100);
        $product->setReserved(reserved: 0);
        $product->setHelpNeeded(helpNeeded: false);
        $product->setPublished(published: true);

        $manager->persist($product);
        $products[] = $product;

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

            $product->setStock(stock: $faker->numberBetween(int1: 5, int2: 350));
            $product->setHelpNeeded(helpNeeded: $faker->boolean(chanceOfGettingTrue: 25));
            $product->setPublished(published: $faker->boolean(chanceOfGettingTrue: 75));
            $product->setImageName(imageName: 'event.jpeg');

            $date = $faker->dateTimeBetween(startDate: '-3 months', endDate: '-1 day');
            $immutable = \DateTimeImmutable::createFromMutable($date);
            $product->setDeliveryAt($immutable);

            $product->setReserved(reserved: $faker->numberBetween(int1: 0, int2: $product->getStock()));

            $manager->persist($product);
            $products[] = $product;
        }

        $manager->flush();
    }
}
