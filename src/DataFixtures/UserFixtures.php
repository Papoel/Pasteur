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

        // ############################################## 1. BATMAN ##############################################
        $userPresident = new User();

        $userPresident->setFirstname(firstname: 'Bruce');
        $userPresident->setLastname(lastname: 'Wayne');
        $userPresident->setPseudo(pseudo: 'Batman');
        $userPresident->setEmail(
            email: strtolower(
                string: $userPresident->getFirstname() . '.' . $userPresident->getLastname()
            ) .
            '@admin.fr'
        );
        $userPresident->setRoles(['ROLE_SUPER_ADMIN']);
        $hash = $this->passwordHasher->hashPassword($userPresident, plainPassword: 'Password1234!');
        $userPresident->setPassword($hash);

        $date = $faker->dateTimeBetween(startDate: '-10 years');
        $immutable = \DateTimeImmutable::createFromMutable($date);
        $userPresident->setCreatedAt($immutable);

        $userPresident->setTelephone(telephone: '0704191500');
        $userPresident->setAddress(address: '1007 Mountain Drive');
        $userPresident->setPostalCode(postalCode: '00000');
        $userPresident->setTown(town: 'Gotham City.');
        $userPresident->setBirthday(birthday: new \DateTime(datetime: '1914/04/07'));
        $userPresident->setFunction(function: 'Président');

        $manager->persist($userPresident);
        $users[] = $userPresident;

        // ############################################## 2. WEBMASTER ##############################################
        $userAdministrateur = new User();

        $userAdministrateur->setFirstname(firstname: 'Pascal');
        $userAdministrateur->setLastname(lastname: 'Briffard');
        $userAdministrateur->setPseudo(pseudo: 'Papoel');
        $userAdministrateur->setEmail(email: 'papoel@aperp.fr');
        $userAdministrateur->setRoles(['ROLE_SUPER_ADMIN']);
        $hash = $this->passwordHasher->hashPassword($userAdministrateur, plainPassword: 'Password1234!');
        $userAdministrateur->setPassword($hash);
        $userAdministrateur->setCreatedAt(createdAt: new \DateTimeImmutable(datetime: '2022/11/14'));
        $fixed = '06';
        $number = $fixed . random_int(10000000, 99999999);
        $userAdministrateur->setTelephone(telephone: $number);
        $userAdministrateur->setAddress(address: '12 res Kennedy rue des Roquelles');
        $userAdministrateur->setPostalCode(postalCode: '59460');
        $userAdministrateur->setTown(town: 'Jeumont');
        $userAdministrateur->setBirthday(birthday: new \DateTime(datetime: '1985/02/20'));
        $userAdministrateur->setFunction(function: 'Webmaster');

        $manager->persist($userAdministrateur);
        $users[] = $userAdministrateur;

        // ############################################## 3. TRESORIER ##############################################
        $userTresorier = new User();

        $userTresorier->setFirstname(firstname: 'Bruce');
        $userTresorier->setLastname(lastname: $faker->lastName());
        $userTresorier->setEmail(
            email: strtolower(
                string: 'bruce.' . $userTresorier->getLastname()
            ) .
            '@aperp.fr'
        );
        $userTresorier->setRoles(['ROLE_ADMIN']);
        $hash = $this->passwordHasher->hashPassword($userTresorier, plainPassword: 'Password1234!');
        $userTresorier->setPassword($hash);

        $date = $faker->dateTimeBetween(startDate: '-3 years');
        $immutable = \DateTimeImmutable::createFromMutable($date);
        $userTresorier->setCreatedAt($immutable);

        $fixed = '06';
        $number = $fixed . random_int(10000000, 99999999);
        $userTresorier->setTelephone(telephone: $number);
        $userTresorier->setAddress(address: $faker->streetAddress());
        $userTresorier->setPostalCode(postalCode: $faker->numberBetween(int1: 10000, int2: 85500));
        $userTresorier->setTown(town: $faker->city());

        $date = $faker->dateTimeBetween(startDate: '-40 years', endDate: '-15 years');
        //$immutable = \DateTime::createFromImmutable($date);
        $userTresorier->setBirthday($date);
        $userTresorier->setFunction(function: 'Trésorier');

        $manager->persist($userTresorier);
        $users[] = $userTresorier;

        // ############################################## 4. SECRETAIRE ##############################################
        $userSecretaire = new User();

        $userSecretaire->setFirstname(firstname: $faker->firstName());
        $userSecretaire->setLastname(lastname: $faker->lastName());
        $userSecretaire->setEmail(
            email: strtolower(
                string: $userSecretaire->getFirstname() . '.' . $userSecretaire->getLastname()
            ) .
            '@aperp.fr'
        );
        $userSecretaire->setRoles(['ROLE_ADMIN']);
        $hash = $this->passwordHasher->hashPassword($userSecretaire, plainPassword: 'Password1234!');
        $userSecretaire->setPassword($hash);

        $date = $faker->dateTimeBetween(startDate: '-3 years');
        $immutable = \DateTimeImmutable::createFromMutable($date);
        $userSecretaire->setCreatedAt($immutable);

        $fixed = '06';
        $number = $fixed . random_int(10000000, 99999999);
        $userSecretaire->setTelephone(telephone: $number);
        $userSecretaire->setAddress(address: $faker->streetAddress());
        $userSecretaire->setPostalCode(postalCode: $faker->numberBetween(int1: 10000, int2: 85500));
        $userSecretaire->setTown(town: $faker->city());

        $date = $faker->dateTimeBetween(startDate: '-40 years', endDate: '-15 years');
        //$immutable = \DateTimeImmutable::createFromMutable($date);
        $userSecretaire->setBirthday($date);
        $userSecretaire->setFunction(function: 'Secrétaire');

        $manager->persist($userSecretaire);
        $users[] = $userSecretaire;

        // ############################################## 5 à 7. USERS ##############################################
        for ($newUser = 1; $newUser <= 3; ++$newUser) {
            $user = new User();

            $user->setFirstname(firstname: $faker->firstName());
            $user->setLastname(lastname: $faker->lastName());

            $user->setEmail(
                email: strtolower(
                    string: $user->getFirstname() . '.' . $user->getLastname()
                ) .
                '@aperp.fr'
            );

            $hash = $this->passwordHasher->hashPassword($user, plainPassword: 'Password1234!');
            $user->setPassword($hash);

            $date = $faker->dateTimeBetween(startDate: '-3 years');
            $immutable = \DateTimeImmutable::createFromMutable($date);
            $user->setCreatedAt($immutable);

            $fixed = '06';
            $number = $fixed . random_int(10000000, 99999999);
            $user->setTelephone(telephone: $number);

            $user->setAddress(address: $faker->streetAddress());
            $user->setComplementAddress(complementAddress: $faker->word());
            $user->setTown(town: $faker->city());
            $user->setPostalCode(postalCode: $faker->numberBetween(int1: 10000, int2: 85500));
            $user->setPseudo(pseudo: $faker->userName());

            $date = $faker->dateTimeBetween(startDate: '-40 years', endDate: '-15 years');
            //$immutable = \DateTimeImmutable::createFromMutable($date);
            $user->setBirthday($date);

            $manager->persist($user);
            $users[] = $user;
        }

        $manager->flush();
    }
}
