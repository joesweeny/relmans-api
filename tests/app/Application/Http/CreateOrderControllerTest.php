<?php

namespace Relmans\Application\Http;

use GuzzleHttp\Psr7\ServerRequest;
use League\Tactician\CommandBus;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Ramsey\Uuid\Uuid;
use Relmans\Boundary\Command\CreateOrderCommand;
use Relmans\Boundary\Utility\OrderItemData;
use Relmans\Domain\Entity\Address;
use Relmans\Domain\Entity\Customer;
use Relmans\Domain\Entity\OrderMethod;
use Relmans\Domain\Enum\FulfilmentType;
use Relmans\Framework\Exception\ValidationException;

class CreateOrderControllerTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var CommandBus|ObjectProphecy
     */
    private $commandBus;
    private CreateOrderController $controller;

    public function setUp(): void
    {
        $this->commandBus = $this->prophesize(CommandBus::class);
        $this->controller = new CreateOrderController($this->commandBus->reveal());
    }

    public function test_invoke_returns_a_201_response_containing_created_order_id()
    {
        $body = (object) [
            'orderNumber' => 'YTE129191A',
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
            'method' => (object) [
                'type' => 'DELIVERY',
                'date' => '2020-03-12T12:00:00+00:00',
                'fee' => 250,
            ],
            'items' => [
                (object) [
                    'productId' => '4c9dd4ce-f8b0-4a24-b2ea-f29295dc8552',
                    'priceId' => '9af64fc1-6168-4859-99ba-a8173fab472c',
                    'price' => 100,
                    'quantity' => 10,
                ]
            ],
        ];

        $request = new ServerRequest(
            'POST',
            '/order',
            ['Content-Type' => 'application/json'],
            json_encode($body)
        );

        $commandAssertion = Argument::that(function (CreateOrderCommand $command) {
            $customer = new Customer(
                'Joe',
                'Sweeny',
                new Address(
                    '58 Holwick Close',
                    'Templetown',
                    'In the ghetto',
                    'Consett',
                    'Durham',
                    'DH87UJ'
                ),
                '07939843048',
                'joe@email.com'
            );

            $orderMethod = new OrderMethod(
                FulfilmentType::DELIVERY(),
                new \DateTimeImmutable('2020-03-12T12:00:00+00:00'),
                250
            );

            $orderItems = [
                new OrderItemData(
                    Uuid::fromString('4c9dd4ce-f8b0-4a24-b2ea-f29295dc8552'),
                    Uuid::fromString( '9af64fc1-6168-4859-99ba-a8173fab472c'),
                    100,
                    10
                ),
            ];

            $this->assertEquals('YTE129191A', $command->getOrderNumber());
            $this->assertEquals($customer, $command->getCustomer());
            $this->assertEquals($orderMethod, $command->getMethod());
            $this->assertEquals($orderItems, $command->getItems());
            return true;
        });

        $this->commandBus->handle($commandAssertion)
            ->shouldBeCalled()
            ->willReturn('YTE129191A');

        $response = $this->controller->__invoke($request);

        $expectedBody = (object) [
            'status' => 'success',
            'data' => (object) [
                'id' => 'YTE129191A',
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

        $this->commandBus->handle(Argument::type(CreateOrderCommand::class))->shouldNotBeCalled();

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

        $this->commandBus->handle(Argument::type(CreateOrderCommand::class))->shouldNotBeCalled();

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
            'orderNumber' => 'YTE129191A',
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
            'method' => (object) [
                'type' => 'DELIVERY',
                'date' => '2020-03-12T12:00:00+00:00',
                'fee' => 250,
            ],
            'items' => [
                (object) [
                    'productId' => '4c9dd4ce-f8b0-4a24-b2ea-f29295dc8552',
                    'priceId' => '9af64fc1-6168-4859-99ba-a8173fab472c',
                    'price' => 100,
                    'quantity' => 10,
                ]
            ],
        ];

        $request = new ServerRequest(
            'POST',
            '/order',
            ['Content-Type' => 'application/json'],
            json_encode($body)
        );

        $this->commandBus->handle(Argument::type(CreateOrderCommand::class))->shouldNotBeCalled();

        $response = $this->controller->__invoke($request);

        $expectedBody = (object) [
            'status' => 'fail',
            'errors' => [
                (object) [
                    'code' => 1,
                    'message' => "'firstName' field is required",
                ]
            ],
        ];

        $this->assertEquals(422, $response->getStatusCode());
        $this->assertEquals($expectedBody, json_decode($response->getBody()->getContents()));
    }

    public function test_invoke_returns_a_422_response_if_command_bus_throws_a_ValidationException()
    {
        $body = (object) [
            'orderNumber' => 'YTE129191A',
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
            'method' => (object) [
                'type' => 'DELIVERY',
                'date' => '2020-03-12T12:00:00+00:00',
                'fee' => 250,
            ],
            'items' => [
                (object) [
                    'productId' => '4c9dd4ce-f8b0-4a24-b2ea-f29295dc8552',
                    'priceId' => '9af64fc1-6168-4859-99ba-a8173fab472c',
                    'price' => 100,
                    'quantity' => 10,
                ]
            ],
        ];

        $request = new ServerRequest(
            'POST',
            '/order',
            ['Content-Type' => 'application/json'],
            json_encode($body)
        );

        $commandAssertion = Argument::that(function (CreateOrderCommand $command) {
            $customer = new Customer(
                'Joe',
                'Sweeny',
                new Address(
                    '58 Holwick Close',
                    'Templetown',
                    'In the ghetto',
                    'Consett',
                    'Durham',
                    'DH87UJ'
                ),
                '07939843048',
                'joe@email.com'
            );

            $orderMethod = new OrderMethod(
                FulfilmentType::DELIVERY(),
                new \DateTimeImmutable('2020-03-12T12:00:00+00:00'),
                250
            );

            $orderItems = [
                new OrderItemData(
                    Uuid::fromString('4c9dd4ce-f8b0-4a24-b2ea-f29295dc8552'),
                    Uuid::fromString( '9af64fc1-6168-4859-99ba-a8173fab472c'),
                    100,
                    10
                ),
            ];

            $this->assertEquals('YTE129191A', $command->getOrderNumber());
            $this->assertEquals($customer, $command->getCustomer());
            $this->assertEquals($orderMethod, $command->getMethod());
            $this->assertEquals($orderItems, $command->getItems());
            return true;
        });

        $this->commandBus->handle($commandAssertion)
            ->shouldBeCalled()
            ->willThrow(new ValidationException('Validation failed'));

        $response = $this->controller->__invoke($request);

        $expectedBody = (object) [
            'status' => 'fail',
            'errors' => [
                (object) [
                    'code' => 1,
                    'message' => 'Validation failed',
                ]
            ],
        ];

        $this->assertEquals(422, $response->getStatusCode());
        $this->assertEquals($expectedBody, json_decode($response->getBody()->getContents()));
    }
}
