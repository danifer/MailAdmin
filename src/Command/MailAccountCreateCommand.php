<?php

namespace App\Command;

use App\Entity\MailAccount;
use App\Repository\DomainRepository;
use App\Service\PasswordHasher;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsCommand(
    name: 'app:mail-account:create',
    description: 'Creates a new mail account',
)]
class MailAccountCreateCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ValidatorInterface $validator,
        private DomainRepository $domainRepository,
        private PasswordHasher $passwordHasher,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'The email address')
            ->addArgument('password', InputArgument::REQUIRED, 'The password')
            ->addArgument('domain', InputArgument::REQUIRED, 'The domain name');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        // Get and validate domain
        $domainName = $input->getArgument('domain');
        $domain = $this->domainRepository->findOneBy(['domainName' => $domainName]);
        
        if (!$domain) {
            $io->error(sprintf('Domain "%s" not found.', $domainName));
            return Command::FAILURE;
        }

        // Create and configure the mail account
        $mailAccount = new MailAccount();
        $mailAccount->setEmail($input->getArgument('email'));
        $mailAccount->setPassword(
            $this->passwordHasher->hashPassword($input->getArgument('password'))
        );
        $mailAccount->setDomain($domain);

        // Validate the entity
        $errors = $this->validator->validate($mailAccount);
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $io->error($error->getMessage());
            }
            return Command::FAILURE;
        }

        // Save to database
        $this->entityManager->persist($mailAccount);
        $this->entityManager->flush();

        $io->success(sprintf('Mail account "%s" was created successfully.', $mailAccount->getEmail()));

        return Command::SUCCESS;
    }
}
