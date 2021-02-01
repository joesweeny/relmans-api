<?php

namespace Relmans\Application\Console;

use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\Tools\Console\Command\DumpSchemaCommand;
use Doctrine\Migrations\Tools\Console\Command\ExecuteCommand;
use Doctrine\Migrations\Tools\Console\Command\GenerateCommand;
use Doctrine\Migrations\Tools\Console\Command\LatestCommand;
use Doctrine\Migrations\Tools\Console\Command\ListCommand;
use Doctrine\Migrations\Tools\Console\Command\MigrateCommand;
use Doctrine\Migrations\Tools\Console\Command\RollupCommand;
use Doctrine\Migrations\Tools\Console\Command\StatusCommand;
use Doctrine\Migrations\Tools\Console\Command\SyncMetadataCommand;
use Doctrine\Migrations\Tools\Console\Command\VersionCommand;
use Relmans\Application\Console\Command\CategoryCreateCommand;
use Relmans\Application\Console\Command\HelloCommand;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Console
{
    /**
     * @var ContainerInterface
     */
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function run(InputInterface $input, OutputInterface $output): int
    {
        $app = new Application();

        $app->setAutoExit(false);
        $app->setCatchExceptions(false);

        $factory = $this->container->get(DependencyFactory::class);

        $app->addCommands([
            new HelloCommand(),
            $this->container->get(CategoryCreateCommand::class),
            new DumpSchemaCommand($factory),
            new ExecuteCommand($factory),
            new GenerateCommand($factory),
            new LatestCommand($factory),
            new ListCommand($factory),
            new MigrateCommand($factory),
            new RollupCommand($factory),
            new StatusCommand($factory),
            new SyncMetadataCommand($factory),
            new VersionCommand($factory),
        ]);

        try {
            return $app->run($input, $output);
        } catch (\Exception $e) {
            $output->write($e->getMessage());
            return Command::FAILURE;
        }
    }
}
