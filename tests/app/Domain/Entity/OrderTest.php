<?php

namespace app\Domain\Entity;

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Relmans\Domain\Entity\Address;
use Relmans\Domain\Entity\Customer;
use Relmans\Domain\Entity\Order;
use Relmans\Domain\Entity\OrderItem;
use Relmans\Domain\Entity\OrderValue;
use Relmans\Domain\Enum\OrderStatus;

class OrderTest extends TestCase
{
    public function test_order_class_can_be_instantiated()
    {
        $id = Uuid::uuid4();
        $externalId = '12345678';
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
        $value = new OrderValue(10000, 20);
        $item = new OrderItem(
            Uuid::uuid4(),
            Uuid::uuid4(),
            40,
            5
        );
        $createdAt = new \DateTimeImmutable();
        $updatedAt = new \DateTimeImmutable();

        $order = new Order(
            $id,
            $externalId,
            $customer,
            $status,
            $value,
            [$item],
            $createdAt,
            $updatedAt
        );

        $this->assertEquals($id, $order->getId());
        $this->assertEquals($externalId, $order->getExternalId());
        $this->assertEquals($customer, $order->getCustomer());
        $this->assertEquals($status, $order->getStatus());
        $this->assertEquals($value, $order->getValue());
        $this->assertEquals([$item], $order->getItems());
        $this->assertEquals($createdAt, $order->getCreatedAt());
        $this->assertEquals($updatedAt, $order->getUpdatedAt());
    }
}
