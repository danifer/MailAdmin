<?php

namespace App\Command;

use App\Repository\MailAliasRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:mail-alias:delete',
    description: 'Deletes a mail alias by source address',
)]
class MailAliasDeleteCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private MailAliasRepository $mailAliasRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('source', InputArgument::REQUIRED, 'The source email address to delete');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $source = $input->getArgument('source');

        $mailAlias = $this->mailAliasRepository->findOneBy(['source' => $source]);
        if (!$mailAlias) {
            $io->error(sprintf('Mail alias with source "%s" not found.', $source));
            return Command::FAILURE;
        }

        if (!$io->confirm(sprintf('Are you sure you want to delete mail alias from "%s" to "%s"?', 
            $mailAlias->getSource(), 
            $mailAlias->getDestination()
        ), false)) {
            $io->note('Operation cancelled.');
            return Command::SUCCESS;
        }

        $this->entityManager->remove($mailAlias);
        $this->entityManager->flush();

        $io->success(sprintf('Mail alias "%s" was deleted successfully.', $source));

        return Command::SUCCESS;
    }
}
