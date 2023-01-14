<?php

declare(strict_types=1);

namespace App\Tests\Functional\Repository\User;

use App\Repository\User\UserRepository;
use JetBrains\PhpStorm\NoReturn;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserRepositoryTest extends WebTestCase
{
    public function testFindAllMethodReturn7Users(): void
    {
        $client = static::createClient();

        /** @var UserRepository $userRepository */
        $userRepository = $client
            ->getContainer()
            ->get(id: UserRepository::class);

        /** @var UserRepository $user */
        $user = $userRepository->findAll();

        self::assertCount(expectedCount: 7, haystack: $user);
    }

    #[NoReturn] public function testFindOneByReturn1User(): void
    {
        $client = static::createClient();

        /** @var UserRepository $userRepository */
        $userRepository = $client
            ->getContainer()
            ->get(id: UserRepository::class);

        /** @var UserRepository $user */
        $user = $userRepository->findOneBy(criteria: ['firstname' => 'pascal', 'lastname' => 'briffard']);

        self::assertNotNull($user);
        self::assertSame(
            expected: 'pascal.briffard@aperp.fr',
            actual: $user->getEmail(),
            message: 'L\'email de l\'utilisateur n\'est pas le même que celui attendu.'
        );
    }

    public function testFindByFirstnameBruceReturn2Users(): void
    {
        $client = static::createClient();

        /** @var UserRepository $userRepository */
        $userRepository = $client
            ->getContainer()
            ->get(id: UserRepository::class);

        /** @var UserRepository $user */
        $users = $userRepository->findBy(criteria: ['firstname' => 'bruce']);

        self::assertCount(expectedCount: 2, haystack: $users);
    }

    /** Pour un test vert il faut initialiser la base de données le jour du test
     * Dans les Fixtures 3 users ont été crées avec la date d'anniversaire du jour
     */
    public function testBirthdayMethodReturn3Users(): void
    {
        $client = static::createClient();

        /** @var UserRepository $userRepository */
        $userRepository = $client
            ->getContainer()
            ->get(id: UserRepository::class);

        /** @var UserRepository $user */
        $users = $userRepository->findByBirthday();

        self::assertSame(
            expected: 3 ,
            actual: count($users) ,
            message: 'Il n\'y a pas 3 utilisateurs qui ont leur anniversaire aujourd\'hui.'
        );
    }

    public function testFindByRoleMethod(): void
    {
        $client = static::createClient();

        /** @var UserRepository $userRepository */
        $userRepository = $client
            ->getContainer()
            ->get(id: UserRepository::class);

        /** @var UserRepository $user */
        $roleUsers = $userRepository->findByRole(role: 'ROLE_USER');
        $roleSuperAdmin = $userRepository->findByRole(role: 'ROLE_SUPER_ADMIN');
        $roleAdmin = $userRepository->findByRole(role: 'ROLE_ADMIN');

        self::assertCount(expectedCount: 3, haystack: $roleUsers);
        self::assertCount(expectedCount: 2, haystack: $roleSuperAdmin);
        self::assertCount(expectedCount: 2, haystack: $roleAdmin);
    }
}
