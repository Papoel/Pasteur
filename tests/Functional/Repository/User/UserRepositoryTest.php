<?php

declare(strict_types=1);

namespace App\Tests\Functional\Repository\User;

use App\Entity\User\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserRepositoryTest extends KernelTestCase
{
    public function testAddUsersAndCheckIfTheyAreCorrectlyInsert(): void
    {
        $kernel = self::bootKernel();
        $container = $kernel->getContainer();
        $manager = $container->get(id: 'doctrine')->getManager();

        // Create President
        $testPresident = new User();
        $testPresident->setEmail(email: 'president@email.fr');
        $testPresident->setPassword(password: 'Password1234!');
        $testPresident->setRoles(roles: ['ROLE_PRESIDENT']);
        $testPresident->setFirstname(firstname: 'Test');
        $testPresident->setLastname(lastname: 'President');
        $testPresident->setCreatedAt(createdAt: new \DateTimeImmutable());
        $testPresident->setUpdatedAt(updatedAt: new \DateTimeImmutable());
        $manager->persist($testPresident);

        // Create Webmaster
        $testAdmin = new User();
        $testAdmin->setEmail(email: 'webmaster@email.fr');
        $testAdmin->setPassword(password: 'Password1234!');
        $testAdmin->setRoles(roles: ['ROLE_WEBMASTER']);
        $testAdmin->setFirstname(firstname: 'Test');
        $testAdmin->setLastname(lastname: 'Webmaster');
        $testAdmin->setCreatedAt(createdAt: new \DateTimeImmutable());
        $testAdmin->setUpdatedAt(updatedAt: new \DateTimeImmutable());
        $manager->persist($testAdmin);

        $manager->flush();

        // Check if the user is in the database
        $president = $container->get(id: 'doctrine')
            ->getRepository(persistentObject: User::class)
            ->findOneBy(['email' => 'president@email.fr']);
        self::assertNotNull($president);

        $webmaster = $container->get(id: 'doctrine')
            ->getRepository(persistentObject: User::class)
            ->findOneBy(['email' => 'webmaster@email.fr']);
        self::assertNotNull($webmaster);

        self::AssertCount(expectedCount: 2, haystack: $manager
            ->getRepository(entityName: User::class)->findAll());
    }

    public function testRoles(): void
    {
        $kernel = self::bootKernel();
        $container = $kernel->getContainer();
        $manager = $container->get(id: 'doctrine')->getManager();
        // Travailler avec des fixtures ?....
        self::AssertCount(expectedCount: 0, haystack: $manager
            ->getRepository(entityName: User::class)->findAll());
    }
}
