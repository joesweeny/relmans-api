<?php

namespace IntelligenceFusion\Actor\Traits;

use IntelligenceFusion\Actor\Bootstrap\Config;
use IntelligenceFusion\Actor\Bootstrap\ConfigFactory;
use IntelligenceFusion\Actor\Bootstrap\ContainerFactory;
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
