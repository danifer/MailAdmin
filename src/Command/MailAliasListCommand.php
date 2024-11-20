<?php

namespace App\Command;

use App\Repository\MailAliasRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:mail-alias:list',
    description: 'Lists all mail aliases',
)]
class MailAliasListCommand extends Command
{
    public function __construct(
        private MailAliasRepository $mailAliasRepository,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $aliases = $this->mailAliasRepository->findAll();

        if (!$aliases) {
            $io->warning('No mail aliases found.');
            return Command::SUCCESS;
        }

        $rows = [];
        foreach ($aliases as $alias) {
            $rows[] = [
                $alias->getId(),
                $alias->getSource(),
                $alias->getDestination(),
            ];
        }

        $io->table(
            ['ID', 'Source', 'Destination'],
            $rows
        );

        return Command::SUCCESS;
    }
}
