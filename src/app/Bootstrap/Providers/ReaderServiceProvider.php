<?php

namespace Relmans\Bootstrap\Providers;

use Psr\Container\ContainerInterface;
use Relmans\Bootstrap\ServiceProvider;
use Relmans\Domain\Persistence\Doctrine\DoctrineProductReader;
use Relmans\Domain\Persistence\ProductReader;

class ReaderServiceProvider implements ServiceProvider
{
    public function getDefinitions(): array
    {
        return [
            ProductReader::class => \DI\factory(function (ContainerInterface $container) {
                return $container->get(DoctrineProductReader::class);
            })
        ];
    }
}
