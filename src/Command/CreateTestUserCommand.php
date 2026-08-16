<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-test-user',
    description: 'Creates a test user with a securely hashed password',
)]
class CreateTestUserCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
    ) {
        parent::__construct();
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $io = new SymfonyStyle($input, $output);

        $email = 'test@example.com';
        $plainPassword = 'TestPassword123!';

        // Check whether the test user already exists
        $existingUser = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => $email]);

        if ($existingUser !== null) {
            $io->warning('Test user already exists.');
            return Command::SUCCESS;
        }

        // Create user
        $user = new User();

        $user->setEmail($email);
        $user->setFirstName('Test');
        $user->setLastName('User');
        $user->setIsActive(true);

        // Hash the password
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $plainPassword
        );

        $user->setPassword($hashedPassword);

        // Persist user
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success('Test user created successfully.');

        $io->listing([
            'Email: ' . $email,
            'Password: ' . $plainPassword,
            'Active: ' . ($user->isActive() ? 'Yes' : 'No'),
            'ID: ' . $user->getId(),
        ]);

        return Command::SUCCESS;
    }
}