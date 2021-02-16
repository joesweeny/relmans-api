<?php

namespace app\Application\Http;

use GuzzleHttp\Psr7\ServerRequest;
use League\Tactician\CommandBus;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Relmans\Application\Http\CreateCategoryController;
use Relmans\Boundary\Command\CreateCategoryCommand;

class CreateCategoryControllerTest extends TestCase
{
    use ProphecyTrait;

    /** @var CommandBus|ObjectProphecy  */
    private $commandBus;
    private CreateCategoryController $controller;

    public function setUp(): void
    {
        $this->commandBus = $this->prophesize(CommandBus::class);
        $this->controller = new CreateCategoryController($this->commandBus->reveal());
    }

    public function test_invoke_returns_a_201_response_with_a_response_body_containing_the_new_product_id()
    {
        $body = (object) [
            'name' => 'Fruit',
        ];

        $request = new ServerRequest(
            'POST',
            '/category',
            ['Content-Type' => 'application/json'],
            json_encode($body)
        );

        $commandAssertion = Argument::that(function (CreateCategoryCommand $command) use ($body) {
            $this->assertEquals($body->name, $command->getName());
            return true;
        });

        $this->commandBus->handle($commandAssertion)
            ->shouldBeCalled()
            ->willReturn('52cb1724-92d8-45c4-ba94-1a770c45c6db');

        $response = $this->controller->__invoke($request);

        $expectedBody = (object) [
            'status' => 'success',
            'data' => (object) [
                'id' => '52cb1724-92d8-45c4-ba94-1a770c45c6db',
            ]
        ];

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals($expectedBody, json_decode($response->getBody()->getContents()));
    }

    public function test_invoke_returns_a_400_response_if_unable_to_parse_request_body()
    {
        $request = new ServerRequest(
            'POST',
            '/product',
            ['Content-Type' => 'application/json'],
            '{"invalidJson": "yes"'
        );

        $this->commandBus->handle(Argument::type(CreateCategoryCommand::class))->shouldNotBeCalled();

        $response = $this->controller->__invoke($request);

        $expectedBody = (object) [
            'status' => 'fail',
            'errors' => [
                (object) [
                    'code' => 1,
                    'message' => 'Syntax error',
                ]
            ],
        ];

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals($expectedBody, json_decode($response->getBody()->getContents()));
    }

    public function test_invoke_returns_a_400_response_if_request_body_is_empty()
    {
        $request = new ServerRequest(
            'POST',
            '/product',
            ['Content-Type' => 'application/json'],
            json_encode('')
        );

        $this->commandBus->handle(Argument::type(CreateCategoryCommand::class))->shouldNotBeCalled();

        $response = $this->controller->__invoke($request);

        $expectedBody = (object) [
            'status' => 'fail',
            'errors' => [
                (object) [
                    'code' => 1,
                    'message' => 'Unable to parse request body',
                ]
            ],
        ];

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals($expectedBody, json_decode($response->getBody()->getContents()));
    }

    public function test_invoke_returns_a_422_response_if_request_body_does_not_contain_the_expected_schema()
    {
        $body = (object) [
            'name' => '',
        ];

        $request = new ServerRequest(
            'POST',
            '/product',
            ['Content-Type' => 'application/json'],
            json_encode($body)
        );

        $this->commandBus->handle(Argument::type(CreateCategoryCommand::class))->shouldNotBeCalled();

        $response = $this->controller->__invoke($request);

        $expectedBody = (object) [
            'status' => 'fail',
            'errors' => [
                (object) [
                    'code' => 1,
                    'message' => "'name' field is required",
                ]
            ],
        ];

        $this->assertEquals(422, $response->getStatusCode());
        $this->assertEquals($expectedBody, json_decode($response->getBody()->getContents()));
    }
}
