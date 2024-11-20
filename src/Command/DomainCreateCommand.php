<?php

namespace App\Command;

use App\Entity\Domain;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsCommand(
    name: 'app:domain:create',
    description: 'Creates a new domain',
)]
class DomainCreateCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ValidatorInterface $validator,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('domain-name', InputArgument::REQUIRED, 'The domain name');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $domainName = $input->getArgument('domain-name');

        $domain = new Domain();
        $domain->setDomainName($domainName);

        $errors = $this->validator->validate($domain);
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $io->error($error->getMessage());
            }
            return Command::FAILURE;
        }

        $this->entityManager->persist($domain);
        $this->entityManager->flush();

        $io->success(sprintf('Domain "%s" was created successfully.', $domainName));

        return Command::SUCCESS;
    }
}
