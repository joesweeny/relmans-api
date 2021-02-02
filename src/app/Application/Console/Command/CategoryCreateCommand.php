<?php

namespace Relmans\Application\Console\Command;

use League\Tactician\CommandBus;
use Relmans\Boundary\Command\CreateCategoryCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CategoryCreateCommand extends Command
{
    private CommandBus $commandBus;

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $console = new SymfonyStyle($input, $output);

        $name = $input->getArgument('name');

        $command = new CreateCategoryCommand($name);

        $this->commandBus->handle($command);

        $console->success("Category {$name} created");

        return Command::SUCCESS;
    }

    protected function configure(): void
    {
        $this->setName('category:create')
            ->setDescription('Create a new category resource')
            ->addArgument('name', InputArgument::REQUIRED, 'Name of the category');
    }
}
