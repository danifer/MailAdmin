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
use Symfony\Component\Validator\Constraints\Email;
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
            ->addArgument('destination', InputArgument::REQUIRED, 'The destination email addresses (comma-separated)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $source = $input->getArgument('source');
        $destination = $input->getArgument('destination');
        
        // Validate email addresses
        $emailConstraint = new Email(['message' => 'The email {{ value }} is not a valid email address.']);
        
        $sourceErrors = $this->validator->validate($source, $emailConstraint);
        if (count($sourceErrors) > 0) {
            $io->error($sourceErrors[0]->getMessage());
            return Command::FAILURE;
        }
        
        $destinationErrors = $this->validator->validate($destination, $emailConstraint);
        if (count($destinationErrors) > 0) {
            $io->error($destinationErrors[0]->getMessage());
            return Command::FAILURE;
        }
        
        // Create and configure the mail alias
        $mailAlias = new MailAlias();
        $mailAlias->setSource($input->getArgument('source'));
        $mailAlias->setDestination($input->getArgument('destination'));

        // Validate the entity
        $errors = $this->validator->validate($mailAlias);
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $io->error($error->getMessage());
            }
            return Command::FAILURE;
        }

        // Find and associate the mail account based on the destination email
        $mailAccount = $this->entityManager->getRepository(\App\Entity\MailAccount::class)
            ->findOneBy(['email' => $mailAlias->getDestination()]);
        
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
