<?php

namespace IntelligenceFusion\Actor\Bootstrap\Providers;

use IntelligenceFusion\Actor\Bootstrap\ServiceProvider;
use IntelligenceFusion\Actor\Framework\CommandBus\BaseNameExtractor;
use IntelligenceFusion\Actor\Framework\CommandBus\ContainerHandlerLocator;
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
                        'IntelligenceFusion\\Actor\\Boundary\\Command\\Handler\\'
                    ),
                    new HandleInflector()
                );

                return new CommandBus([$handlerMiddleware]);
            })
        ];
    }
}
