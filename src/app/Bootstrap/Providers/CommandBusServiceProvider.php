<?php

namespace Relmans\Bootstrap\Providers;

use Relmans\Bootstrap\ServiceProvider;
use Relmans\Framework\CommandBus\BaseNameExtractor;
use Relmans\Framework\CommandBus\ContainerHandlerLocator;
use League\Tactician\CommandBus;
use League\Tactician\Handler\CommandHandlerMiddleware;
use League\Tactician\Handler\MethodNameInflector\HandleInflector;
use Psr\Container\ContainerInterface;

class CommandBusServiceProvider implements ServiceProvider
{
    public function getDefinitions(): array
    {
        return [
            CommandBus::class => \DI\factory(function (ContainerInterface $container) {
                $handlerMiddleware = new CommandHandlerMiddleware(
                    new BaseNameExtractor(),
                    new ContainerHandlerLocator(
                        $container,
                        'Relmans\\Boundary\\Command\\Handler\\'
                    ),
                    new HandleInflector()
                );

                return new CommandBus([$handlerMiddleware]);
            })
        ];
    }
}
