<?php

namespace App\Command;

use App\Entity\MailAlias;
use App\Repository\DomainRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsCommand(
    name: 'app:mail-alias:create',
    description: 'Creates a new mail alias',
)]
class MailAliasCreateCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ValidatorInterface $validator,
        private DomainRepository $domainRepository,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('source', InputArgument::REQUIRED, 'The source email address')
            ->addArgument('destination', InputArgument::REQUIRED, 'The destination email address')
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

        // Create and configure the mail alias
        $mailAlias = new MailAlias();
        $mailAlias->setSource($input->getArgument('source'));
        $mailAlias->setDestination($input->getArgument('destination'));
        $mailAlias->setDomain($domain);

        // Validate the entity
        $errors = $this->validator->validate($mailAlias);
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $io->error($error->getMessage());
            }
            return Command::FAILURE;
        }

        // Find and associate the mail account based on the first destination email
        $destinations = array_map('trim', explode(',', $mailAlias->getDestination()));
        $firstDestination = $destinations[0];
        $mailAccount = $this->entityManager->getRepository(\App\Entity\MailAccount::class)
            ->findOneBy(['email' => $firstDestination]);
        
        if ($mailAccount) {
            $mailAlias->setMailAccount($mailAccount);
        }

        // Save to database
        $this->entityManager->persist($mailAlias);
        $this->entityManager->flush();

        $io->success(sprintf('Mail alias from "%s" to "%s" was created successfully.', 
            $mailAlias->getSource(), 
            $mailAlias->getDestination()
        ));

        return Command::SUCCESS;
    }
}
