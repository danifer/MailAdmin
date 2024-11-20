<?php

namespace App\Command;

use App\Repository\DomainRepository;
use App\Repository\MailAccountRepository;
use App\Repository\MailAliasRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:domain:delete',
    description: 'Deletes a domain by name',
)]
class DomainDeleteCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private DomainRepository $domainRepository,
        private MailAccountRepository $mailAccountRepository,
        private MailAliasRepository $mailAliasRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('domain-name', InputArgument::REQUIRED, 'The domain name to delete');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $domainName = $input->getArgument('domain-name');

        $domain = $this->domainRepository->findOneBy(['domainName' => $domainName]);
        if (!$domain) {
            $io->error(sprintf('Domain "%s" not found.', $domainName));
            return Command::FAILURE;
        }

        $mailAccounts = $domain->getMailAccounts();
        if (count($mailAccounts) > 0) {
            $rows = $aliasRows = [];
            foreach ($mailAccounts as $mailAccount) {
                $aliases = $this->mailAliasRepository->findDestinationsContainingString($mailAccount->getEmail());

                foreach ($aliases as $alias) {
                    $aliasRows[] = [
                        $alias->getSource(),
                        $alias->getDestination(),
                    ];
                }

                $rows[] = [
                    $mailAccount->getEmail()
                ];
            }
            $io->section('The following mail accounts that will be deleted:');
            $io->table(['Email'], $rows);

            $io->section('The following mail aliases that will be deleted:');
            $io->table(['Source', 'Destination'], $aliasRows);
        }

        if (!$io->confirm(sprintf('Are you sure you want to delete domain "%s"?', $domain->getDomainName()), false)) {
            $io->note('Operation cancelled.');
            return Command::SUCCESS;
        }

        foreach ($mailAccounts as $mailAccount) {
            $aliases = $this->mailAliasRepository->findDestinationsContainingString($mailAccount->getEmail());

            foreach ($aliases as $alias) {
                $alias->removeFromDestination($mailAccount->getEmail());

                if (empty($alias->getDestination())) {
                    $this->entityManager->remove($alias);
                } else {
                    $this->entityManager->persist($alias);
                }
            }

            $this->entityManager->remove($mailAccount);
        }

        $this->entityManager->remove($domain);
        $this->entityManager->flush();

        $io->success(sprintf('Domain "%s" was deleted successfully.', $domain->getDomainName()));

        return Command::SUCCESS;
    }
}
