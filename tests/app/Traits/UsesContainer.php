<?php

namespace Relmans\Traits;

use Relmans\Bootstrap\Config;
use Relmans\Bootstrap\ConfigFactory;
use Relmans\Bootstrap\ContainerFactory;
use Psr\Container\ContainerInterface;

trait UsesContainer
{
    /**
     * @param Config|null $config
     * @return ContainerInterface
     * @throws \Exception
     */
    public function createContainer(Config $config = null): ContainerInterface
    {
        return (new ContainerFactory())->create($config ?? ConfigFactory::create());
    }
}
