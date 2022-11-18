<?php

declare(strict_types=1);


namespace App\DataFixtures;

use App\Entity\PlagesHoraires\PlagesHoraires;
use Doctrine\Persistence\ObjectManager;

class PlagesHorairesFixtures extends \Doctrine\Bundle\FixturesBundle\Fixture
{
    public function load(ObjectManager $manager)
    {
        $plages = [];
        $plage = new PlagesHoraires();

        // For each hour from 08:00 to 20:00
        for ($hour = 8; $hour <= 20; ++$hour) {
            $plage = new PlagesHoraires();
            $plage->setStartsAt(startsAt: new \DateTime(datetime: $hour . ':00:00'));
            $plage->setEndsAt(endsAt: new \DateTime(datetime: ($hour + 1) . ':00:00'));

            $manager->persist($plage);
            $plages[] = $plage;
        }

        $manager->flush();
    }
}
