<?php

namespace App\DataFixtures;

use App\Entity\User\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        $users = [];
        $userAdmin = new User();

        $userAdmin->setFirstname(firstname: 'Aude');
        $userAdmin->setLastname(lastname: 'Lamy');
        $userAdmin->setEmail(
            email: strtolower(
                string: $userAdmin->getFirstname().'.'.$userAdmin->getLastname()
            ).
            '@aperp.fr'
        );
        $userAdmin->setRoles(['ROLE_PRESIDENT']);
        $hash = $this->passwordHasher->hashPassword($userAdmin, plainPassword: 'Password1234!');
        $userAdmin->setPassword($hash);
        $userAdmin->setCreatedAt(createdAt: new \DateTimeImmutable(datetime: '2015/11/28'));
        $userAdmin->setTelephone(telephone: '0650017440');
        $userAdmin->setAddress(address: '15 rue de la Liberté');
        $userAdmin->setPostalCode(postalCode: '59600');
        $userAdmin->setTown(town: 'Maubeuge');

        $manager->persist($userAdmin);
        $users[] = $userAdmin;

        for ($newUser = 1; $newUser <= 3; ++$newUser) {
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

            $fixed = '06';
            $number = $fixed.random_int(10000000, 99999999);
            $user->setTelephone(telephone: $number);

            $user->setAddress(address: $faker->streetAddress());
            $user->setComplementAddress(complementAddress: $faker->word());
            $user->setTown(town: $faker->city());
            $user->setPostalCode(postalCode: $faker->numberBetween(int1: 10000, int2: 85500));
            $user->setPseudo(pseudo: $faker->userName());

            $manager->persist($user);
            $users[] = $user;
        }

        $manager->flush();
    }
}
