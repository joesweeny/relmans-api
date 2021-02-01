<?php

namespace Relmans\Framework\CommandBus;

use DI\NotFoundException;
use League\Tactician\Exception\MissingHandlerException;
use League\Tactician\Handler\Locator\HandlerLocator;
use Psr\Container\ContainerInterface;

class ContainerHandlerLocator implements HandlerLocator
{
    private ContainerInterface $container;
    private string $rootDirectory;

    public function __construct(ContainerInterface $container, string $rootDirectory)
    {
        $this->container = $container;
        $this->rootDirectory = $rootDirectory;
    }

    public function getHandlerForCommand($commandName): object
    {
        try {
            return $this->container->get($this->rootDirectory . "{$commandName}Handler");
        } catch (NotFoundException $e) {
            throw new MissingHandlerException($e->getMessage());
        }
    }
}
