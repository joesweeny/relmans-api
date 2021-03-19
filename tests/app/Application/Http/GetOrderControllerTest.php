<?php

namespace Relmans\Application\Http;

use Laminas\Diactoros\ServerRequest;
use League\Tactician\CommandBus;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Ramsey\Uuid\Uuid;
use Relmans\Boundary\Command\GetOrderCommand;
use Relmans\Framework\Exception\NotFoundException;

class GetOrderControllerTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var CommandBus|ObjectProphecy
     */
    private $commandBus;
    private GetOrderController $controller;

    public function setUp(): void
    {
        $this->commandBus = $this->prophesize(CommandBus::class);
        $this->controller = new GetOrderController($this->commandBus->reveal());
    }

    public function test_invoke_returns_a_200_response_containing_order_data()
    {
        $order = (object) [
            'id' => '12345678',
            'transactionId' => 'ID9991111',
            'customer' => (object) [
                'firstName' => 'Joe',
                'lastName' => 'Sweeny',
                'address' => (object) [
                    'line1' => '58 Holwick Close',
                    'line2' => 'Templetown',
                    'line3' => 'In the ghetto',
                    'town' => 'Consett',
                    'county' => 'Durham',
                    'postCode' => 'DH87UJ',
                ],
                'phone' => '07939843048',
                'email' => 'joe@email.com',
            ],
            'status' => 'CONFIRMED',
            'method' => (object) [
                'type' => 'DELIVERY',
                'date' => '2020-03-12T12:00:00+00:00',
                'fee' => 250,
            ],
            'items' => [
                (object) [
                    'id' => '34a4c42c-ea99-4500-aa14-4851cbe9e790',
                    'orderId' => '9af64fc1-6168-4859-99ba-a8173fab472c',
                    'productId' => '4c9dd4ce-f8b0-4a24-b2ea-f29295dc8552',
                    'name' => 'Cabbage',
                    'price' => 10,
                    'size' => 1,
                    'measurement' => 'EACH',
                    'quantity' => 100,
                    'createdAt' => '2020-03-12T12:00:00+00:00',
                    'updatedAt' => '2020-03-12T12:00:00+00:00',
                ]
            ],
            'createdAt' => '2020-03-12T12:00:00+00:00',
            'updatedAt' => '2020-03-12T12:00:00+00:00',
        ];

        $request = (new ServerRequest())->withAttribute('id', '12345678');

        $commandAssertion = Argument::that(function (GetOrderCommand $command) {
            $this->assertEquals('12345678', $command->getOrderId());
            return true;
        });

        $this->commandBus->handle($commandAssertion)
            ->shouldBeCalled()
            ->willReturn($order);

        $response = $this->controller->__invoke($request);

        $expected = (object) [
            'status' => 'success',
            'data' => (object) [
                'order' => $order,
            ]
        ];

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($expected, json_decode($response->getBody()->getContents()));
    }

    public function test_invoke_returns_a_404_response_if_command_bus_throws_a_NotFoundException()
    {
        $request = (new ServerRequest())->withAttribute('id', '12345678');

        $commandAssertion = Argument::that(function (GetOrderCommand $command) {
            $this->assertEquals('12345678', $command->getOrderId());
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
}
