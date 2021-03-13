<?php

namespace Relmans\Application\Http;

use Laminas\Diactoros\ServerRequest;
use League\Tactician\CommandBus;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Ramsey\Uuid\Uuid;
use Relmans\Application\Http\DeleteProductController;
use Relmans\Boundary\Command\DeleteProductCommand;

class DeleteProductControllerTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var CommandBus|ObjectProphecy
     */
    private $commandBus;
    private DeleteProductController $controller;

    public function setUp(): void
    {
        $this->commandBus = $this->prophesize(CommandBus::class);
        $this->controller = new DeleteProductController($this->commandBus->reveal());
    }

    public function test_invoke_deletes_a_product_record_via_the_command_bus()
    {
        $request = (new ServerRequest())->withAttribute('id', '3e478e96-8851-4474-8def-4a5027a7d272');

        $commandAssertion = Argument::that(function (DeleteProductCommand $command) {
            $this->assertEquals(Uuid::fromString('3e478e96-8851-4474-8def-4a5027a7d272'), $command->getProductId());
            return true;
        });

        $this->commandBus->handle($commandAssertion)->shouldBeCalled();

        $response = $this->controller->__invoke($request);

        $this->assertEquals(204, $response->getStatusCode());
    }

    public function test_invoke_returns_a_404_response_if_id_provided_is_not_a_valid_uuid_String()
    {
        $request = (new ServerRequest())->withAttribute('id', '3');

        $this->commandBus->handle(Argument::type(DeleteProductCommand::class))->shouldNotBeCalled();

        $response = $this->controller->__invoke($request);

        $expectedBody = (object) [
            'status' => 'fail',
            'errors' => [
                (object) [
                    'code' => 1,
                    'message' => 'Invalid UUID string: 3',
                ]
            ],
        ];

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals($expectedBody, json_decode($response->getBody()->getContents()));
    }
}
