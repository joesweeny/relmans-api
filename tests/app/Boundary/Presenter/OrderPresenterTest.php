<?php

namespace app\Boundary\Presenter;

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Relmans\Boundary\Presenter\OrderPresenter;
use Relmans\Domain\Entity\Address;
use Relmans\Domain\Entity\Customer;
use Relmans\Domain\Entity\Order;
use Relmans\Domain\Entity\OrderItem;
use Relmans\Domain\Entity\OrderMethod;
use Relmans\Domain\Enum\FulfilmentType;
use Relmans\Domain\Enum\Measurement;
use Relmans\Domain\Enum\OrderStatus;

class OrderPresenterTest extends TestCase
{
    public function test_toObject_returns_a_scalar_representation_of_an_Order_domain_object()
    {
        $id = '12345678';
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
        $status = OrderStatus::ACCEPTED();
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

        $order = new Order(
            $id,
            $transactionId,
            $customer,
            $status,
            $method,
            [$item],
            $createdAt,
            $updatedAt
        );

        $scalar = (new OrderPresenter())->toObject($order);

        $customer = (object) [
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
        ];

        $method = (object) [
            'type' => 'DELIVERY',
            'date' => '2020-03-12T12:00:00+00:00',
            'fee' => 250,
        ];

        $items = [
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
        ];

        $this->assertEquals('12345678', $scalar->id);
        $this->assertEquals('ID9991111', $scalar->transactionId);
        $this->assertEquals($customer, $scalar->customer);
        $this->assertEquals('ACCEPTED', $scalar->status);
        $this->assertEquals($method, $scalar->method);
        $this->assertEquals($items, $scalar->items);
        $this->assertEquals('2020-03-12T12:00:00+00:00', $scalar->createdAt);
        $this->assertEquals('2020-03-12T12:00:00+00:00', $scalar->updatedAt);
    }
}
