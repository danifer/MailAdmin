<?php

namespace App\Command;

use App\Repository\MailAccountRepository;
use App\Service\PasswordHasher;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:mail-account:change-password',
    description: 'Changes the password for a mail account',
)]
class MailAccountChangePasswordCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private MailAccountRepository $mailAccountRepository,
        private PasswordHasher $passwordHasher,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'The email address of the account')
            ->addArgument('password', InputArgument::REQUIRED, 'The new password');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');
        $newPassword = $input->getArgument('password');

        $mailAccount = $this->mailAccountRepository->findOneBy(['email' => $email]);
        if (!$mailAccount) {
            $io->error(sprintf('Mail account "%s" not found.', $email));
            return Command::FAILURE;
        }

        if (!$io->confirm(sprintf('Are you sure you want to change the password for "%s"?', $email), false)) {
            $io->note('Operation cancelled.');
            return Command::SUCCESS;
        }

        $mailAccount->setPassword(
            $this->passwordHasher->hashPassword($newPassword)
        );

        $this->entityManager->flush();

        $io->success(sprintf('Password changed successfully for account "%s".', $email));

        return Command::SUCCESS;
    }
}
