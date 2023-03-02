<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\CreateAdminUserCommand;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreateAdminUserCommandTest extends TestCase
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasher;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(originalClassName: EntityManagerInterface::class);
        $this->passwordHasher = $this->createMock(originalClassName: UserPasswordHasherInterface::class);
    }

    /** @test */
    public function CreateAdminWithCommand(): void
    {
        $this->entityManager->expects($this->once())
            ->method(constraint: 'persist')
            ->with($this->callback(function ($user) {
                $this->assertEquals(expected: 'John', actual: $user->getFirstname());
                $this->assertEquals(expected: 'Doe', actual: $user->getLastname());
                $this->assertEquals(expected: 'john.doe@example.com', actual: $user->getEmail());
                $this->assertEquals(expected: ['ROLE_ADMIN', 'ROLE_USER'], actual: $user->getRoles());
                $this->assertEquals(expected: 'webmaster', actual: $user->getFunction());
                return true;
            }));

        $this->passwordHasher->expects($this->once())
            ->method(constraint: 'hashPassword')
            ->willReturn(value: 'hashed_password');

        $command = new CreateAdminUserCommand($this->entityManager, $this->passwordHasher);
        $commandTester = new CommandTester($command);

        $commandTester->setInputs([
            'John', // prénom
            'Doe', // nom
            'john.doe@example.com', // email
            'password', // mot de passe
            0, // choix ROLE_ADMIN
            7, // choix webmaster
        ]);
        $commandTester->execute([]);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString(needle: 'Administrateur créé.', haystack: $output);
    }

}
