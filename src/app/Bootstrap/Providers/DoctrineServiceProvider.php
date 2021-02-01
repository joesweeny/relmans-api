<?php

namespace IntelligenceFusion\Actor\Bootstrap\Providers;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Doctrine\Migrations\Configuration\Connection\ExistingConnection;
use Doctrine\Migrations\Configuration\Migration\ConfigurationArray;
use Doctrine\Migrations\DependencyFactory;
use IntelligenceFusion\Actor\Bootstrap\Config;
use IntelligenceFusion\Actor\Bootstrap\ServiceProvider;
use Psr\Container\ContainerInterface;

class DoctrineServiceProvider implements ServiceProvider
{
    public function getDefinitions(): array
    {
        return [
            Connection::class => \DI\factory(function (ContainerInterface $container) {
                $config = $container->get(Config::class);

                $params = [
                    'dbname' => $config->get('database.default.name'),
                    'user' => $config->get('database.default.user'),
                    'password' => $config->get('database.default.password'),
                    'host' => $config->get('database.default.host'),
                    'driver' => $config->get('database.default.driver'),
                ];

                return DriverManager::getConnection($params);
            }),

            AbstractSchemaManager::class => \DI\factory(function (ContainerInterface $container) {
                return $container->get(Connection::class)->getSchemaManager();
            }),

            DependencyFactory::class => \DI\factory(function (ContainerInterface $container) {
                $connection = $container->get(Connection::class);

                $configuration = new ConfigurationArray([
                    'migrations_paths' => [
                        'IntelligenceFusion\Actor\Application\Console\Migrations' => __DIR__ . '/../../Application/Console/Migrations',
                    ],

                    'table_storage' => [
                        'table_name' => 'doctrine_migration_versions',
                        'version_column_name' => 'version',
                        'version_column_length' => 1024,
                        'executed_at_column_name' => 'executed_at',
                        'execution_time_column_name' => 'execution_time',
                    ],

                    'all_or_nothing' => true,
                    'check_database_platform' => true,
                    'organize_migrations' => 'none',
                ]);

                return DependencyFactory::fromConnection($configuration, new ExistingConnection($connection));
            })
        ];
    }
}
