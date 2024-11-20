<?php

namespace App\Command;

use App\Repository\MailAccountRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:mail-account:list',
    description: 'Lists all mail accounts',
)]
class MailAccountListCommand extends Command
{
    public function __construct(
        private MailAccountRepository $mailAccountRepository,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $accounts = $this->mailAccountRepository->findAll();

        if (!$accounts) {
            $io->warning('No mail accounts found.');
            return Command::SUCCESS;
        }

        $rows = [];
        foreach ($accounts as $account) {
            $rows[] = [
                $account->getId(),
                $account->getEmail(),
                $account->getDomain()->getDomainName(),
            ];
        }

        $io->table(
            ['ID', 'Email', 'Domain'],
            $rows
        );

        return Command::SUCCESS;
    }
}
