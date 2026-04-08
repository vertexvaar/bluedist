<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSeed\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;
use VerteXVaaR\BlueSeed\SeedService;

#[AsCommand('app:seed')]
class SeedCommand extends Command
{
    public function __construct(
        private readonly SeedService $seedService,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument(
            'name',
            InputArgument::OPTIONAL,
            'Name of the seeder to run. If not provided, lists all available seeders.',
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument('name');
        if (null === $name) {
            $output->writeln('Available seeders:');
            foreach ($this->seedService->seeders as $seederName => $seeder) {
                $output->writeln('  - ' . $seederName);
            }
            return Command::SUCCESS;
        }

        try {
            $this->seedService->seed($name);
            $output->writeln(sprintf('Successfully seeded "%s"', $name));
        } catch (Throwable $e) {
            $output->writeln(sprintf('Error seeding "%s": %s', $name, $e->getMessage()));
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
