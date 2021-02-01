<?php

namespace IntelligenceFusion\Actor\Traits;

use Doctrine\DBAL\Schema\AbstractSchemaManager;
use IntelligenceFusion\Actor\Application\Console\Console;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;

trait RunsMigrations
{
    public function runMigrations(ContainerInterface $container): ContainerInterface
    {
        $schema = $container->get(AbstractSchemaManager::class);

        foreach ($schema->listTableNames() as $table) {
            $schema->dropTable($table);
        }

        $console = $container->get(Console::class);

        $input = new StringInput("migrations:migrate --no-interaction --quiet");
        $output = new BufferedOutput();

        $console->run($input, $output);

        if ($output->fetch()) {
            throw new \RuntimeException("Migration command failed: {$output->fetch()}");
        }

        return $container;
    }
}
