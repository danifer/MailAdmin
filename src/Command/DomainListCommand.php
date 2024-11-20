<?php

namespace App\Command;

use App\Repository\DomainRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:domain:list',
    description: 'Lists all domains',
)]
class DomainListCommand extends Command
{
    public function __construct(
        private DomainRepository $domainRepository,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $domains = $this->domainRepository->findAll();

        if (!$domains) {
            $io->warning('No domains found.');
            return Command::SUCCESS;
        }

        $rows = [];
        foreach ($domains as $domain) {
            $rows[] = [
                $domain->getId(),
                $domain->getDomainName(),
            ];
        }

        $io->table(
            ['ID', 'Domain Name'],
            $rows
        );

        return Command::SUCCESS;
    }
}
