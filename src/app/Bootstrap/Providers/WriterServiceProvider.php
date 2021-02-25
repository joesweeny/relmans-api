<?php

namespace Relmans\Bootstrap\Providers;

use Psr\Container\ContainerInterface;
use Relmans\Bootstrap\ServiceProvider;
use Relmans\Domain\Persistence\Doctrine\DoctrineOrderWriter;
use Relmans\Domain\Persistence\Doctrine\DoctrineProductWriter;
use Relmans\Domain\Persistence\OrderWriter;
use Relmans\Domain\Persistence\ProductWriter;

class WriterServiceProvider implements ServiceProvider
{
    public function getDefinitions(): array
    {
        return [
            OrderWriter::class => \DI\factory(function (ContainerInterface $container) {
                return $container->get(DoctrineOrderWriter::class);
            }),

            ProductWriter::class => \DI\factory(function (ContainerInterface $container) {
                return $container->get(DoctrineProductWriter::class);
            })
        ];
    }
}
