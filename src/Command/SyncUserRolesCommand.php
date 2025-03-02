<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:sync-user-roles')]
class SyncUserRolesCommand extends Command
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $users = $this->entityManager->getRepository(User::class)->findAll();
        $updatedCount = 0;

        foreach ($users as $user) {
            $roles = $user->getRoles();
            $isAdmin = in_array('ROLE_ADMIN', $roles);

            // Ensure admin_verified matches the role
            if ($user->isAdminVerified() !== $isAdmin) {
                $user->setAdminVerified($isAdmin);
                $updatedCount++;
            }
        }

        if ($updatedCount > 0) {
            $this->entityManager->flush();
            $output->writeln("<info>Updated {$updatedCount} users successfully.</info>");
        } else {
            $output->writeln("<comment>No updates needed.</comment>");
        }

        return Command::SUCCESS;
    }
}
