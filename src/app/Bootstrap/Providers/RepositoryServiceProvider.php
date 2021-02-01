<?php

namespace Relmans\Bootstrap\Providers;

use Psr\Container\ContainerInterface;
use Relmans\Bootstrap\ServiceProvider;
use Relmans\Domain\Persistence\CategoryRepository;
use Relmans\Domain\Persistence\Doctrine\DoctrineCategoryRepository;

class RepositoryServiceProvider implements ServiceProvider
{
    public function getDefinitions(): array
    {
        return [
            CategoryRepository::class => \DI\factory(function (ContainerInterface $container) {
                return $container->get(DoctrineCategoryRepository::class);
            })
        ];
    }
}
