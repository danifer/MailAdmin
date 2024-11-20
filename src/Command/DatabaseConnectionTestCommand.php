<?php

namespace App\Command;

use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:database:test',
    description: 'Tests the database connection and shows connection details',
)]
class DatabaseConnectionTestCommand extends Command
{
    public function __construct(
        private Connection $connection,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            // Get connection params (without password)
            $params = $this->connection->getParams();
            unset($params['password']);

            $io->section('Database Connection Parameters');
            $io->table(
                ['Parameter', 'Value'],
                array_map(
                    fn($k, $v) => [$k, is_array($v) ? json_encode($v) : (string)$v],
                    array_keys($params),
                    array_values($params)
                )
            );

            // Test connection
            $this->connection->connect();
            
            if ($this->connection->isConnected()) {
                $io->success([
                    'Database connection successful!'
                ]);
                
                return Command::SUCCESS;
            }
        } catch (\Exception $e) {
            $io->error([
                'Database connection failed!',
                sprintf('Error: %s', $e->getMessage()),
                '',
                'Common solutions:',
                '- Check that the database server is running',
                '- Verify database credentials in .env or .env.local',
                '- Confirm the database exists and user has access',
                sprintf('- Try connecting directly: mysql -u%s -p -h%s %s', 
                    $params['user'] ?? 'user',
                    $params['host'] ?? 'localhost', 
                    $params['dbname'] ?? 'database'
                )
            ]);

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
