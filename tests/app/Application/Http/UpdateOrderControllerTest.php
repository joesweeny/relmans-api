<?php

namespace Relmans\Application\Http;

use GuzzleHttp\Psr7\ServerRequest;
use League\Tactician\CommandBus;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Http\Message\ServerRequestInterface;
use Relmans\Boundary\Command\UpdateOrderCommand;
use Relmans\Domain\Enum\OrderStatus;
use Relmans\Framework\Exception\NotFoundException;

class UpdateOrderControllerTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var CommandBus|ObjectProphecy
     */
    private $commandBus;
    private UpdateOrderController $controller;

    public function setUp(): void
    {
        $this->commandBus = $this->prophesize(CommandBus::class);
        $this->controller = new UpdateOrderController($this->commandBus->reveal());
    }

    public function test_invoke_returns_a_204_response()
    {
        $body = (object) [
            'status' => 'ACCEPTED',
        ];

        $request = $this->request(json_encode($body));

        $commandAssertion = Argument::that(function (UpdateOrderCommand $command) {
            $this->assertEquals('ORD7890', $command->getId());
            $this->assertEquals(OrderStatus::ACCEPTED(), $command->getStatus());
            return true;
        });

        $this->commandBus->handle($commandAssertion)->shouldBeCalled();

        $response = $this->controller->__invoke($request);

        $this->assertEquals(204, $response->getStatusCode());
        $this->assertEmpty($response->getBody()->getContents());
    }

    public function test_invoke_returns_a_400_response_if_unable_to_parse_request_body()
    {
        $request = $this->request('{"invalidJson": "yes"');

        $this->commandBus->handle(Argument::type(UpdateOrderCommand::class))->shouldNotBeCalled();

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
        $request = $this->request(null);

        $this->commandBus->handle(Argument::type(UpdateOrderCommand::class))->shouldNotBeCalled();

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

    public function test_invoke_returns_a_422_response_if_request_body_does_not_contain_the_expected_schema()
    {
        $body = (object) [
            'status' => 'INVALID',
        ];

        $request = $this->request(json_encode($body));

        $this->commandBus->handle(Argument::type(UpdateOrderCommand::class))->shouldNotBeCalled();

        $response = $this->controller->__invoke($request);

        $expectedBody = (object) [
            'status' => 'fail',
            'errors' => [
                (object) [
                    'code' => 1,
                    'message' => "Value 'INVALID' is not part of the enum Relmans\Domain\Enum\OrderStatus",
                ]
            ],
        ];

        $this->assertEquals(422, $response->getStatusCode());
        $this->assertEquals($expectedBody, json_decode($response->getBody()->getContents()));
    }

    public function test_invoke_returns_a_404_response_if_order_resource_does_not_exist()
    {
        $body = (object) [
            'status' => 'ACCEPTED',
        ];

        $request = $this->request(json_encode($body));

        $commandAssertion = Argument::that(function (UpdateOrderCommand $command) {
            $this->assertEquals('ORD7890', $command->getId());
            $this->assertEquals(OrderStatus::ACCEPTED(), $command->getStatus());
            return true;
        });

        $this->commandBus->handle($commandAssertion)
            ->shouldBeCalled()
            ->willThrow(new NotFoundException('Order does not exist'));

        $response = $this->controller->__invoke($request);

        $expectedBody = (object) [
            'status' => 'fail',
            'errors' => [
                (object) [
                    'code' => 1,
                    'message' => 'Order does not exist',
                ]
            ],
        ];

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals($expectedBody, json_decode($response->getBody()->getContents()));
    }

    private function request(?string $body): ServerRequestInterface
    {
        $request = new ServerRequest('PATCH', '', [], $body);
        return $request->withAttribute('id', 'ORD7890');
    }
}
