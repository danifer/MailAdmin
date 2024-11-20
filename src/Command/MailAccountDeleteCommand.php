<?php

namespace App\Command;

use App\Repository\MailAccountRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:mail-account:delete',
    description: 'Deletes a mail account by email address',
)]
class MailAccountDeleteCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private MailAccountRepository $mailAccountRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('email', InputArgument::REQUIRED, 'The email address to delete');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');

        $mailAccount = $this->mailAccountRepository->findOneBy(['email' => $email]);
        if (!$mailAccount) {
            $io->error(sprintf('Mail account "%s" not found.', $email));
            return Command::FAILURE;
        }

        $aliases = $mailAccount->getMailAliases();
        if (count($aliases) > 0) {
            $io->section('This account has the following mail aliases that will also be deleted:');
            $rows = [];
            foreach ($aliases as $alias) {
                $rows[] = [
                    $alias->getSource(),
                    $alias->getDestination()
                ];
            }
            $io->table(['Source', 'Destination'], $rows);
        }

        if (!$io->confirm(sprintf('Are you sure you want to delete mail account "%s" and all its aliases?', $email), false)) {
            $io->note('Operation cancelled.');
            return Command::SUCCESS;
        }

        $this->entityManager->remove($mailAccount);
        $this->entityManager->flush();

        $io->success(sprintf('Mail account "%s" was deleted successfully.', $email));

        return Command::SUCCESS;
    }
}
