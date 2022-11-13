<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {}

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        $users = [];
        $userAdmin = new User();

        $userAdmin->setFirstname(firstname: 'Pascal');
        $userAdmin->setLastname(lastname: 'Briffard');
        $userAdmin->setEmail(
            email: strtolower(
                string: $userAdmin->getFirstname().'.'.$userAdmin->getLastname()
            ).
            '@aperp.fr'
        );
        $userAdmin->setRoles(['ROLE_ADMIN']);
        $hash = $this->passwordHasher->hashPassword($userAdmin, plainPassword: 'password');
        $userAdmin->setPassword($hash);
        $userAdmin->setCreatedAt(createdAt: new \DateTimeImmutable(datetime: '2015/11/28'));

        $manager->persist($userAdmin);
        $users[] = $userAdmin;

        for ($newUser = 1; $newUser <= 10; ++$newUser) {
            $user = new User();

            $user->setFirstname(firstname: $faker->firstName());
            $user->setLastname(lastname: $faker->lastName());

            $user->setEmail(
                email: strtolower(
                    string: $user->getFirstname().'.'.$user->getLastname()
                ).
                '@aperp.fr'
            );


            $user->setRoles(['ROLE_USER']);
            $hash = $this->passwordHasher->hashPassword($user, plainPassword: 'password');
            $user->setPassword($hash);

            $date = $faker->dateTimeBetween(startDate: '-3 years', endDate: 'now');
            $immutable = \DateTimeImmutable::createFromMutable($date);
            $user->setCreatedAt($immutable);

            $manager->persist($user);
            $users[] = $user;
        }

        $manager->flush();
    }
}
