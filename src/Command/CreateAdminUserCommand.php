<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\User\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreateAdminUserCommand extends Command
{
    public const CMD_NAME = 'app:create:admin';

    private SymfonyStyle $io;

    /**
     * @param EntityManagerInterface      $entityManager
     * @param UserPasswordHasherInterface $passwordHasher
     */
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
        parent::__construct(name: self::CMD_NAME);
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return void
     */
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle(input: $input, output: $output);
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $firstName = $this->io->askQuestion(question: new Question(question: 'Prénom'));
        $lastName = $this->io->askQuestion(question: new Question(question: 'Nom'));
        $email = $this->io->askQuestion(question: new Question(question: 'Email'));
        $password = $this->io->askQuestion(question: new Question(question: 'Mot de passe'));
        $role = $this->io->choice(
            question: 'Rôle',
            choices: ['ROLE_ADMIN', 'ROLE_SUPER_ADMIN'],
            default: 'ROLE_ADMIN',
        );
        $function = $this->io->choice(
            question: 'Fonction',
            choices: [
                'president',
                'vice-president',
                'secretaire',
                'vice-secretaire',
                'trésorier',
                'vice-tresorier',
                'membre-actif',
                'webmaster'
            ],
            default: 'membre-actif',
        );

        /** @var User $user */
        $user = new User();
        $user->setFirstname(firstname: $firstName);
        $user->setLastname(lastname: $lastName);
        $user->setEmail(email: $email);
        $user->setPassword(password: $this->passwordHasher->hashPassword(user: $user, plainPassword: $password));
        $user->setRoles(roles: [$role]);
        $user->setFunction(function: $function);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->io->success('Administrateur créé.');

        return self::SUCCESS;
    }
}
