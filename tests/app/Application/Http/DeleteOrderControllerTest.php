<?php

namespace Relmans\Application\Http;

use Laminas\Diactoros\ServerRequest;
use League\Tactician\CommandBus;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Relmans\Boundary\Command\DeleteOrderCommand;

class DeleteOrderControllerTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var CommandBus|ObjectProphecy
     */
    private $commandBus;
    private DeleteOrderController $controller;

    public function setUp(): void
    {
        $this->commandBus = $this->prophesize(CommandBus::class);
        $this->controller = new DeleteOrderController($this->commandBus->reveal());
    }

    public function test_invoke_deletes_an_order_record_via_the_command_bus()
    {
        $request = (new ServerRequest())->withAttribute('id', '3e478e96');

        $commandAssertion = Argument::that(function (DeleteOrderCommand $command) {
            $this->assertEquals('3e478e96', $command->getOrderId());
            return true;
        });

        $this->commandBus->handle($commandAssertion)->shouldBeCalled();

        $response = $this->controller->__invoke($request);

        $this->assertEquals(204, $response->getStatusCode());
    }
}
