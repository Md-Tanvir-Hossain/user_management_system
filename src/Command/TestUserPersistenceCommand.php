<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:test-user-persistence',
    description: 'Tests User entity persistence with PostgreSQL',
)]
class TestUserPersistenceCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $io = new SymfonyStyle($input, $output);

        $io->title('User Persistence Test');

        // 1. Create a test user
        $user = new User();

        $user->setFirstName('Test');
        $user->setLastName('User');
        $user->setEmail('persistence-test@example.com');
        $user->setPassword('test-password');

        // 2. Persist the user
        $this->entityManager->persist($user);

        // 3. Flush = INSERT into PostgreSQL
        $this->entityManager->flush();

        $io->success('User successfully inserted into PostgreSQL.');

        $userId = $user->getId();

        $io->text('Created User ID: ' . $userId);

        // 4. Clear EntityManager memory
        $this->entityManager->clear();

        // 5. Query the user again from PostgreSQL
        $savedUser = $this->entityManager
            ->getRepository(User::class)
            ->find($userId);

        if ($savedUser === null) {
            $io->error('User could not be found after insertion.');
            return Command::FAILURE;
        }

        // 6. Verify the data
        $io->section('Retrieved User');

        $io->listing([
            'ID: ' . $savedUser->getId(),
            'Name: ' . $savedUser->getFirstName() . ' ' . $savedUser->getLastName(),
            'Email: ' . $savedUser->getEmail(),
            'Roles: ' . implode(', ', $savedUser->getRoles()),
            'Active: ' . ($savedUser->isActive() ? 'Yes' : 'No'),
        ]);

        // 7. Delete the test user
        $this->entityManager->remove($savedUser);

        // 8. Flush = DELETE from PostgreSQL
        $this->entityManager->flush();

        $io->success('Test user successfully deleted from PostgreSQL.');

        // 9. Verify deletion
        $deletedUser = $this->entityManager
            ->getRepository(User::class)
            ->find($userId);

        if ($deletedUser !== null) {
            $io->error('Test user still exists in the database.');
            return Command::FAILURE;
        }

        $io->success('Persistence test completed successfully!');

        return Command::SUCCESS;
    }
}