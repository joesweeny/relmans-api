<?php

namespace Relmans\Bootstrap\Providers;

use Psr\Container\ContainerInterface;
use Relmans\Bootstrap\ServiceProvider;
use Relmans\Domain\Persistence\Doctrine\DoctrineProductWriter;
use Relmans\Domain\Persistence\ProductWriter;

class WriterServiceProvider implements ServiceProvider
{
    public function getDefinitions(): array
    {
        return [
            ProductWriter::class => \DI\factory(function (ContainerInterface $container) {
                return $container->get(DoctrineProductWriter::class);
            })
        ];
    }
}
