<?php

namespace Relmans\Domain\Entity;

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Relmans\Domain\Entity\OrderValue;
use Relmans\Domain\Enum\OrderStatus;

class OrderTest extends TestCase
{
    public function test_order_class_can_be_instantiated()
    {
        $id = Uuid::uuid4();
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
            '07939843048'
        );
        $status = OrderStatus::CONFIRMED();
        $method = new OrderMethod('delivery', new \DateTimeImmutable(), 250);
        $item = new OrderItem(
            Uuid::uuid4(),
            Uuid::uuid4(),
            'Cabbage',
            1,
            'each',
            100,
            5
        );
        $createdAt = new \DateTimeImmutable();
        $updatedAt = new \DateTimeImmutable();

        $order = new Order(
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

        $this->assertEquals($id, $order->getId());
        $this->assertEquals($externalId, $order->getExternalId());
        $this->assertEquals($transactionId, $order->getTransactionId());
        $this->assertEquals($customer, $order->getCustomer());
        $this->assertEquals($status, $order->getStatus());
        $this->assertEquals($method, $order->getMethod());
        $this->assertEquals([$item], $order->getItems());
        $this->assertEquals($createdAt, $order->getCreatedAt());
        $this->assertEquals($updatedAt, $order->getUpdatedAt());
    }
}
