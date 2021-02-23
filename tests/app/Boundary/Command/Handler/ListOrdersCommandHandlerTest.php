<?php

namespace Relmans\Boundary\Command\Handler;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Ramsey\Uuid\Uuid;
use Relmans\Boundary\Command\ListOrdersCommand;
use Relmans\Boundary\Presenter\OrderPresenter;
use Relmans\Domain\Entity\Address;
use Relmans\Domain\Entity\Customer;
use Relmans\Domain\Entity\Order;
use Relmans\Domain\Entity\OrderItem;
use Relmans\Domain\Entity\OrderMethod;
use Relmans\Domain\Enum\FulfilmentType;
use Relmans\Domain\Enum\Measurement;
use Relmans\Domain\Enum\OrderStatus;
use Relmans\Domain\Persistence\OrderReader;
use Relmans\Domain\Persistence\OrderReaderQuery;

class ListOrdersCommandHandlerTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var OrderReader|ObjectProphecy
     */
    private $reader;
    private ListOrdersCommandHandler $handler;

    public function setUp(): void
    {
        $this->reader = $this->prophesize(OrderReader::class);
        $this->handler = new ListOrdersCommandHandler(
            $this->reader->reveal(),
            new OrderPresenter()
        );
    }

    public function test_handle_returns_an_array_of_scalar_order_objects()
    {
        $command = new ListOrdersCommand(
            'DH87UJ',
            'ORDER111',
            '2020-03-12T12:00:00+00:00',
            '2020-03-13T12:00:00+00:00',
            '2020-03-14T12:00:00+00:00',
            '2020-03-15T12:00:00+00:00',
            'created_at_desc'
        );

        $queryAssertion = Argument::that(function (OrderReaderQuery $query) {
            $this->assertEquals('DH87UJ', $query->getPostCode());
            $this->assertEquals('ORDER111', $query->getOrderNumber());
            $this->assertEquals(new \DateTimeImmutable('2020-03-12T12:00:00+00:00'), $query->getDeliveryDateFrom());
            $this->assertEquals(new \DateTimeImmutable('2020-03-13T12:00:00+00:00'), $query->getDeliveryDateTo());
            $this->assertEquals(new \DateTimeImmutable('2020-03-14T12:00:00+00:00'), $query->getOrderDateFrom());
            $this->assertEquals(new \DateTimeImmutable('2020-03-15T12:00:00+00:00'), $query->getOrderDateTo());
            $this->assertEquals('created_at_desc', $query->getOrderBy());
            return true;
        });

        $this->reader->get($queryAssertion)
            ->shouldBeCalled()
            ->willReturn([$this->order()]);

        $orders = $this->handler->handle($command);

        $expected = [
            (object) [
                'id' => '9af64fc1-6168-4859-99ba-a8173fab472c',
                'externalId' => '12345678',
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
            ]
        ];

        $this->assertEquals($expected, $orders);
    }

    public function test_handle_can_handle_nullable_fields_from_command()
    {
        $command = new ListOrdersCommand(
            null,
            null,
            null,
            null,
            null,
            null,
            null
        );

        $queryAssertion = Argument::that(function (OrderReaderQuery $query) {
            $this->assertNull($query->getPostCode());
            $this->assertNull($query->getOrderNumber());
            $this->assertNull($query->getDeliveryDateFrom());
            $this->assertNull($query->getDeliveryDateTo());
            $this->assertNull($query->getOrderDateFrom());
            $this->assertNull($query->getOrderDateTo());
            $this->assertNull($query->getOrderBy());
            return true;
        });

        $this->reader->get($queryAssertion)
            ->shouldBeCalled()
            ->willReturn([$this->order()]);

        $orders = $this->handler->handle($command);

        $expected = [
            (object) [
                'id' => '9af64fc1-6168-4859-99ba-a8173fab472c',
                'externalId' => '12345678',
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
            ]
        ];

        $this->assertEquals($expected, $orders);
    }

    private function order(): Order
    {
        $id = Uuid::fromString('9af64fc1-6168-4859-99ba-a8173fab472c');
        $externalId = '12345678';
        $transactionId = 'ID9991111';
        $address = new Address(
            '58 Holwick Close',
            'Templetown',
            'In the ghetto',
            'Consett',
            'Durham',
            'DH87UJ'
        );
        $customer = new Customer(
            'Joe',
            'Sweeny',
            $address,
            '07939843048',
            'joe@email.com'
        );
        $status = OrderStatus::CONFIRMED();
        $method = new OrderMethod(
            FulfilmentType::DELIVERY(),
            new \DateTimeImmutable('2020-03-12T12:00:00+00:00'),
            250
        );
        $item = new OrderItem(
            Uuid::fromString('34a4c42c-ea99-4500-aa14-4851cbe9e790'),
            $id,
            Uuid::fromString('4c9dd4ce-f8b0-4a24-b2ea-f29295dc8552'),
            'Cabbage',
            10,
            1,
            Measurement::EACH(),
            100,
            new \DateTimeImmutable('2020-03-12T12:00:00+00:00'),
            new \DateTimeImmutable('2020-03-12T12:00:00+00:00'),
        );
        $createdAt = new \DateTimeImmutable('2020-03-12T12:00:00+00:00');
        $updatedAt = new \DateTimeImmutable('2020-03-12T12:00:00+00:00');

        return new Order(
            $id,
            $externalId,
            $transactionId,
            $customer,
            $status,
            $method,
            [$item],
            $createdAt,
            $updatedAt
        );
    }
}
