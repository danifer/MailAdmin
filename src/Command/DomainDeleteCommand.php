<?php

namespace App\Command;

use App\Repository\DomainRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:domain:delete',
    description: 'Deletes a domain',
)]
class DomainDeleteCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private DomainRepository $domainRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('id', InputArgument::REQUIRED, 'The domain ID');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $id = $input->getArgument('id');

        $domain = $this->domainRepository->find($id);
        if (!$domain) {
            $io->error(sprintf('Domain with ID "%s" not found.', $id));
            return Command::FAILURE;
        }

        if (!$io->confirm(sprintf('Are you sure you want to delete domain "%s"?', $domain->getDomainName()), false)) {
            $io->note('Operation cancelled.');
            return Command::SUCCESS;
        }

        $this->entityManager->remove($domain);
        $this->entityManager->flush();

        $io->success(sprintf('Domain "%s" was deleted successfully.', $domain->getDomainName()));

        return Command::SUCCESS;
    }
}
