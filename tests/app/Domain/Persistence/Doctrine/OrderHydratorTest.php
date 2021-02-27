<?php

namespace Relmans\Domain\Persistence\Doctrine;

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Relmans\Domain\Entity\Address;
use Relmans\Domain\Entity\Customer;
use Relmans\Domain\Entity\OrderItem;
use Relmans\Domain\Entity\OrderMethod;
use Relmans\Domain\Enum\FulfilmentType;
use Relmans\Domain\Enum\Measurement;
use Relmans\Domain\Enum\OrderStatus;

class OrderHydratorTest extends TestCase
{
    public function test_hydrateOrder_returns_an_Order_object()
    {
        $items = [
            [
                'id' => '4ba37855-8d91-4f1b-9213-c525ba87767f',
                'order_id' => '12345678',
                'product_id' => '0c4b8f56-5359-4639-af37-cc146dd170ed',
                'name' => 'Kiwi Fruit',
                'price' => 100,
                'size' => 1,
                'measurement' => 'EACH',
                'quantity' => 3,
                'created_at' => 1614078413,
                'updated_at' => 1614078413,
            ]
        ];

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

        $method = new OrderMethod(
            FulfilmentType::DELIVERY(),
            new \DateTimeImmutable('2021-02-23T11:06:51+00:00'),
            250
        );

        $row = (object) [
            'id' => '12345678',
            'transaction_id' => 'TRAN1',
            'customer_details' => json_encode($customer),
            'status' => 'ACCEPTED',
            'method' => json_encode($method),
            'created_at' => 1614078413,
            'updated_at' => 1614078413,
        ];

        $order = (new OrderHydrator())->hydrateOrder($row, $items);

        $this->assertEquals('12345678', $order->getId());
        $this->assertEquals('TRAN1', $order->getTransactionId());
        $this->assertEquals($customer, $order->getCustomer());
        $this->assertEquals(OrderStatus::ACCEPTED(), $order->getStatus());
        $this->assertEquals($method, $order->getMethod());
        $this->assertEquals(new \DateTimeImmutable('2021-02-23T11:06:53+00:00'), $order->getCreatedAt());
        $this->assertEquals(new \DateTimeImmutable('2021-02-23T11:06:53+00:00'), $order->getUpdatedAt());

        $this->assertEquals(Uuid::fromString('4ba37855-8d91-4f1b-9213-c525ba87767f'), $order->getItems()[0]->getId());
        $this->assertEquals(12345678, $order->getItems()[0]->getOrderId());
        $this->assertEquals(Uuid::fromString('0c4b8f56-5359-4639-af37-cc146dd170ed'), $order->getItems()[0]->getProductId());
        $this->assertEquals('Kiwi Fruit', $order->getItems()[0]->getName());
        $this->assertEquals(100, $order->getItems()[0]->getPrice());
        $this->assertEquals(1, $order->getItems()[0]->getSize());
        $this->assertEquals(Measurement::EACH(), $order->getItems()[0]->getMeasurement());
        $this->assertEquals(3, $order->getItems()[0]->getQuantity());
        $this->assertEquals(new \DateTimeImmutable('2021-02-23T11:06:53+00:00'), $order->getItems()[0]->getCreatedAt());
        $this->assertEquals(new \DateTimeImmutable('2021-02-23T11:06:53+00:00'), $order->getItems()[0]->getUpdatedAt());
    }

    public function test_hydrateOrderItem_returns_an_OrderItem_object()
    {
        $row = (object) [
            'id' => '4ba37855-8d91-4f1b-9213-c525ba87767f',
            'order_id' => '12345678',
            'product_id' => '0c4b8f56-5359-4639-af37-cc146dd170ed',
            'name' => 'Kiwi Fruit',
            'price' => 100,
            'size' => 1,
            'measurement' => 'EACH',
            'quantity' => 3,
            'created_at' => 1614078413,
            'updated_at' => 1614078413,
        ];

        $item = (new OrderHydrator())->hydrateOrderItem($row);

        $this->assertEquals(Uuid::fromString('4ba37855-8d91-4f1b-9213-c525ba87767f'), $item->getId());
        $this->assertEquals('12345678', $item->getOrderId());
        $this->assertEquals(Uuid::fromString('0c4b8f56-5359-4639-af37-cc146dd170ed'), $item->getProductId());
        $this->assertEquals('Kiwi Fruit', $item->getName());
        $this->assertEquals(100, $item->getPrice());
        $this->assertEquals(1, $item->getSize());
        $this->assertEquals(Measurement::EACH(), $item->getMeasurement());
        $this->assertEquals(3, $item->getQuantity());
        $this->assertEquals(new \DateTimeImmutable('2021-02-23T11:06:53+00:00'), $item->getCreatedAt());
        $this->assertEquals(new \DateTimeImmutable('2021-02-23T11:06:53+00:00'), $item->getUpdatedAt());
    }
}
