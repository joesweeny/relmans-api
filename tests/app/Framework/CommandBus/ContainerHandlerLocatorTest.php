<?php

namespace IntelligenceFusion\Actor\Framework\CommandBus;

use DI\Container;
use League\Tactician\Exception\MissingHandlerException;
use PHPUnit\Framework\TestCase;

class ContainerHandlerLocatorTest extends TestCase
{
    public function test_getHandlerForCommand_returns_an_object_linked_to_provided_class_name()
    {
        $locator = new ContainerHandlerLocator(new Container(), __NAMESPACE__ . '\\');

        $handler = $locator->getHandlerForCommand((new \ReflectionClass(new MyCommand()))->getShortName());

        $this->assertInstanceOf(MyCommandHandler::class, $handler);
    }

    public function test_getHandlerForCommand_throws_a_MissingHandlerException()
    {
        $locator = new ContainerHandlerLocator(new Container(), '\\');

        $this->expectException(MissingHandlerException::class);
        $locator->getHandlerForCommand((new \ReflectionClass(new MyCommand()))->getShortName());
    }
}

class MyCommand {}
class MyCommandHandler{}
