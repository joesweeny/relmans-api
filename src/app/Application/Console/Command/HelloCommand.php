<?php

namespace Relmans\Application\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HelloCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln("Welcome to the IF Actor Service {$input->getArgument('name')}");

        return Command::SUCCESS;
    }

    protected function configure(): void
    {
        $this->setName('hello')
            ->setDescription('Prints welcome message to user')
            ->addArgument('name', InputArgument::REQUIRED, 'The name to greet');
    }
}
