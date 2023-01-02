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
        $faker = Factory::create(locale: 'fr_FR');

        $events = [];

        // ############################################### APERO PHP ############################################### //
        $event = new Event();
        $event->setName(name: 'Apéro PHP');
        $event->setDescription(description: 'Apéro PHP est un événement mensuel qui a lieu à Maubeuge. 
            Il est organisé par la communauté PHP francophone.');
        $event->setLocation(location: 'Maubeuge');
        $event->setPrice(price: 0);
        $event->setStartsAt(startsAt: new \DateTimeImmutable(datetime: '2023/06/14 11:00'));
        $event->setFinishAt(finishAt: new \DateTimeImmutable(datetime: '2023/06/16 18:30'));
        $randomStatus = random_int(min: 0, max: 3);
        $event->setStatus(Event::STATUS[$randomStatus]);
        $event->setCapacity(capacity: 1);
        $event->setCreatedAt(createdAt: new \DateTimeImmutable(datetime: 'now'));
        $event->setUpdatedAt(updatedAt: new \DateTimeImmutable(datetime: 'now + 1 day'));
        $event->setHelpNeeded(helpNeeded: false);
        $event->setImageName(imageName: 'event.jpeg');

        $manager->persist($event);
        $events[] = $event;

        // ############################################ CHASSE A L'OEUF ############################################ //
        $eventEaster = new Event();
        $eventEaster->setName(name: 'Chasse à l\'oeuf');
        $eventEaster->setDescription(description: 'Il était une fois un garçon nommé Sacha qui aimait particulièrement
        les créatures mystiques.
        Il passait son temps à imaginer toutes sortes de créatures fantastiques et à essayer de les dessiner. 
        Un jour, alors qu\'il était en train de dessiner un oeuf d\'or, il eut une idée brillante : et si cet oeuf 
        était réel et que quelqu\'un avait réussi à le trouver ? 
        Sacha décida alors de partir à la recherche de cet oeuf d\'or mythique.
        Il se mit en quête de toutes les informations qu\'il pouvait trouver sur cet oeuf d\'or.
        Il parcourut de nombreux livres, interrogea ses amis et même demanda l\'aide d\'experts en créatures mystiques.
        Malheureusement, personne ne semblait connaître l\'existence de cet oeuf d\'or.
        Sacha ne se découragea pas pour autant et continua ses recherches avec acharnement.
        Il passait des heures sur Internet, à fouiller les forums et les blogs à la recherche d\'indices.
        Un jour, alors qu\'il était en train de parcourir un vieux livre, il tomba sur une légende qui parlait d\'un 
        oeuf d\' or caché par une créature mythique.
        Selon la légende, celui qui trouverait cet oeuf deviendrait immensément riche.
        Sacha était convaincu que cet oeuf était la clé de ses rêves et décida de partir à sa recherche.
        Il emballa ses affaires et se rendit à l\'endroit où, selon la légende, l\'oeuf d\'or était caché.
        Il parcourut de nombreux kilomètres à travers des paysages sauvages et des forêts luxuriantes, à la recherche
        de cet oeuf d\' or mythique.
        Finalement, après de longues semaines de recherches, Sacha arriva à destination.
        Il se retrouva devant une grotte sombre et mystérieuse.
        Il hésita un instant avant de se décider à entrer.
        Il avança lentement, en prenant soin de ne pas faire de bruit.
        Soudain, il vit une lumière qui brillait au fond de la grotte.
        Il s\'approcha et découvrit un oeuf d\' or brillant, caché au fond de la grotte.
        Sacha était fasciné par cet oeuf d\'or et ne put s\'empêcher de le ramasser.
        Il le serra contre lui et sortit de la grotte en courant.
        Il était heureux et fier d\'avoir trouvé cet oeuf d\' or mythique qui allait lui permettre de 
        réaliser tous ses rêves. 
        Son journal intime mentionne que Sacha a caché cet oeuf quelque part au Sablon, jusqu\'a présent personne n\'a 
        été en mesure de le trouver.');
        $eventEaster->setLocation(location: 'Les Sablons');
        $eventEaster->setPrice(price: 500);
        $eventEaster->setStartsAt(startsAt: new \DateTimeImmutable(datetime: '2023/04/02 10:00'));
        $eventEaster->setFinishAt(finishAt: new \DateTimeImmutable(datetime: '2023/04/02 15:30'));
        $eventEaster->setCapacity(capacity: 100);
        $eventEaster->setCreatedAt(createdAt: new \DateTimeImmutable(datetime: 'now'));
        $eventEaster->setUpdatedAt(updatedAt: new \DateTimeImmutable(datetime: 'now + 3 hours'));
        $eventEaster->setHelpNeeded(helpNeeded: true);
        $eventEaster->setPublished(published: true);
        $eventEaster->setImageName(imageName: 'event.jpeg');

        $manager->persist($eventEaster);
        $events[] = $eventEaster;

        // ################################################ RANDOM ################################################ //
        for ($newEvent = 1; $newEvent <= 10; ++$newEvent) {
            $event = new Event();
            $event->setName(name: $faker->sentence(nbWords: 3, variableNbWords: true));
            $event->setDescription(description: $faker->paragraph(nbSentences: 3, variableNbSentences: true));
            $event->setLocation(location: $faker->city());
            $event->setPrice(price: $faker->numberBetween(int1: 0, int2: 9999));
            $randomStatus = random_int(min: 0, max: 3);
            $event->setStatus(Event::STATUS[$randomStatus]);
            $event->setCapacity(capacity: $faker->numberBetween(10, 100));
            $event->setHelpNeeded(helpNeeded: false);
            $event->setImageName(imageName: 'event.jpeg');

            $date = $faker->dateTimeBetween(startDate: 'now', endDate: '+6 months');
            $immutable = \DateTimeImmutable::createFromMutable($date);
            $event->setStartsAt($immutable);

            $date = $event->getStartsAt();

            $date = $date->modify(modifier: '+' . $faker->numberBetween(int1: 4, int2: 16) . ' hours');
            $event->setFinishAt($date);

            $date = $faker->dateTimeBetween(startDate: '-3 months', endDate: '-1 day');
            $immutable = \DateTimeImmutable::createFromMutable($date);
            $event->setCreatedAt($immutable);

            $date = $faker->dateTimeBetween(startDate: '-3 months', endDate: '-1 day');
            $immutable = \DateTimeImmutable::createFromMutable($date);
            $event->setUpdatedAt($immutable);

            $manager->persist($event);
            $events[] = $event;
        }

        $manager->flush();
    }
}
