<?php

namespace Relmans\Application\Http;

use Laminas\Diactoros\ServerRequest;
use League\Tactician\CommandBus;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Relmans\Boundary\Command\ListOrdersCommand;

class ListOrdersControllerTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var CommandBus|ObjectProphecy
     */
    private $commandBus;
    private ListOrdersController $controller;

    public function setUp(): void
    {
        $this->commandBus = $this->prophesize(CommandBus::class);
        $this->controller = new ListOrdersController($this->commandBus->reveal());
    }

    public function test_invoke_returns_a_200_response_containing_an_array_of_order_data()
    {
        $orders = [
            (object) [
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
                        'orderId' => '12345678',
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
            ]
        ];

        $params = [
            'postCode' => 'DH87UJ',
            'deliveryFrom' => '2020-03-12T12:00:00+00:00',
            'deliveryTo' => '2020-03-13T12:00:00+00:00',
            'orderFrom' => '2020-03-14T12:00:00+00:00',
            'orderTo' => '2020-03-15T12:00:00+00:00',
            'orderBy' => 'created_at_desc',
        ];

        $request = (new ServerRequest())->withQueryParams($params);

        $commandAssertion = Argument::that(function (ListOrdersCommand $command) {
            $this->assertEquals('DH87UJ', $command->getPostCode());
            $this->assertEquals(new \DateTimeImmutable('2020-03-12T12:00:00+00:00'), $command->getDeliveryFrom());
            $this->assertEquals(new \DateTimeImmutable('2020-03-13T12:00:00+00:00'), $command->getDeliveryTo());
            $this->assertEquals(new \DateTimeImmutable('2020-03-14T12:00:00+00:00'), $command->getOrderDateFrom());
            $this->assertEquals(new \DateTimeImmutable('2020-03-15T12:00:00+00:00'), $command->getOrderDateTo());
            $this->assertEquals('created_at_desc', $command->getOrderBy());
            return true;
        });

        $this->commandBus->handle($commandAssertion)
            ->shouldBeCalled()
            ->willReturn($orders);

        $response = $this->controller->__invoke($request);

        $expected = (object) [
            'status' => 'success',
            'data' => (object) [
                'orders' => $orders,
            ]
        ];

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($expected, json_decode($response->getBody()->getContents()));
    }

    public function test_invoke_returns_a_422_response_if_query_parameter_provided_is_not_a_valid_RFC3999_date_string()
    {
        $params = [
            'postCode' => 'DH87UJ',
            'deliveryFrom' => 'Hello',
            'deliveryTo' => '2020-03-13T12:00:00+00:00',
            'orderFrom' => '2020-03-14T12:00:00+00:00',
            'orderTo' => '2020-03-15T12:00:00+00:00',
            'orderBy' => 'created_at_desc',
        ];

        $request = (new ServerRequest())->withQueryParams($params);

        $this->commandBus->handle(Argument::type(ListOrdersCommand::class))->shouldNotBeCalled();

        $response = $this->controller->__invoke($request);

        $expectedBody = (object) [
            'status' => 'fail',
            'errors' => [
                (object) [
                    'code' => 1,
                    'message' => 'Date provided is not a valid RFC3339 valid date',
                ]
            ],
        ];

        $this->assertEquals(422, $response->getStatusCode());
        $this->assertEquals($expectedBody, json_decode($response->getBody()->getContents()));
    }
}
