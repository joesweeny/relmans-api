<?php

namespace Relmans\Boundary\Command\Handler;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Relmans\Boundary\Command\CreateOrderCommand;
use Relmans\Domain\Entity\Order;
use Relmans\Domain\Factory\OrderFactory;
use Relmans\Domain\Persistence\OrderWriter;
use Relmans\Framework\Exception\EmailException;
use Relmans\Framework\Exception\ValidationException;

class CreateOrderCommandHandlerTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var OrderFactory|ObjectProphecy
     */
    private $factory;
    /**
     * @var OrderWriter|ObjectProphecy
     */
    private $writer;
    private CreateOrderCommandHandler $handler;

    public function setUp(): void
    {
        $this->factory = $this->prophesize(OrderFactory::class);
        $this->writer = $this->prophesize(OrderWriter::class);
        $this->handler = new CreateOrderCommandHandler(
            $this->factory->reveal(),
            $this->writer->reveal()
        );
    }

    public function test_handle_creates_and_persists_order_object()
    {
        $address = (object) [
            'line1' => '58 Holwick Close',
            'line2' => 'Templetown',
            'line3' => 'In the ghetto',
            'town' => 'Consett',
            'county' => 'Durham',
            'postCode' => 'DH87UJ',
        ];

        $method = (object) [
            'type' => 'DELIVERY',
            'date' => '2020-03-12T12:00:00+00:00',
            'fee' => 250,
        ];

        $items = [
            (object) [
                'productId' => '4c9dd4ce-f8b0-4a24-b2ea-f29295dc8552',
                'priceId' => '9af64fc1-6168-4859-99ba-a8173fab472c',
                'price' => 100,
                'quantity' => 10,
            ]
        ];

        $command = new CreateOrderCommand(
            'ORDER101091',
            'Joe',
            'Sweeny',
            $address,
            '07939843048',
            'joe@email.com',
            $method,
            $items
        );

        /** @var Order|ObjectProphecy $order */
        $order = $this->prophesize(Order::class);
        $order->getId()->willReturn('ORDER101091');
        $order->getCustomer()->willReturn($command->getCustomer());

        $this->factory->createNewOrder(
            $command->getOrderNumber(),
            $command->getCustomer(),
            $command->getMethod(),
            $command->getItems()
        )->shouldBeCalled()->willReturn($order->reveal());

        $this->writer->insert($order)->shouldBeCalled();

        $id = $this->handler->handle($command);

        $this->assertEquals('ORDER101091', $id);
    }

    public function test_handle_throws_a_ValidationException_if_thrown_by_the_order_factory()
    {
        $address = (object) [
            'line1' => '58 Holwick Close',
            'line2' => 'Templetown',
            'line3' => 'In the ghetto',
            'town' => 'Consett',
            'county' => 'Durham',
            'postCode' => 'DH87UJ',
        ];

        $method = (object) [
            'type' => 'DELIVERY',
            'date' => '2020-03-12T12:00:00+00:00',
            'fee' => 250,
        ];

        $items = [
            (object) [
                'productId' => '4c9dd4ce-f8b0-4a24-b2ea-f29295dc8552',
                'priceId' => '9af64fc1-6168-4859-99ba-a8173fab472c',
                'price' => 100,
                'quantity' => 10,
            ]
        ];

        $command = new CreateOrderCommand(
            'ORDER101091',
            'Joe',
            'Sweeny',
            $address,
            '07939843048',
            'joe@email.com',
            $method,
            $items
        );

        $this->factory->createNewOrder(
            $command->getOrderNumber(),
            $command->getCustomer(),
            $command->getMethod(),
            $command->getItems()
        )
            ->shouldBeCalled()
            ->willThrow(new ValidationException('Invalid'));

        $this->writer->insert(Argument::type(Order::class))->shouldNotBeCalled();

        $this->expectException(ValidationException::class);
        $this->handler->handle($command);
    }
}
