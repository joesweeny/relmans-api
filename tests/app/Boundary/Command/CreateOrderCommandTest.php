<?php

namespace Relmans\Boundary\Command;

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Relmans\Boundary\Utility\OrderItemData;
use Relmans\Domain\Entity\Address;
use Relmans\Domain\Entity\Customer;
use Relmans\Domain\Entity\OrderMethod;
use Relmans\Domain\Enum\FulfilmentType;

class CreateOrderCommandTest extends TestCase
{
    public function test_class_can_be_instatiated()
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

        $this->assertEquals('ORDER101091', $command->getOrderNumber());
        $this->assertEquals($customer, $command->getCustomer());
        $this->assertEquals($orderMethod, $command->getMethod());
        $this->assertEquals($orderItems, $command->getItems());
    }

    public function test_class_can_be_instatianted_handling_nullable_properties()
    {
        $address = (object) [
            'line1' => '58 Holwick Close',
            'postCode' => 'DH87UJ',
        ];

        $method = (object) [
            'type' => 'DELIVERY',
            'date' => '2020-03-12T12:00:00+00:00',
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

        $customer = new Customer(
            'Joe',
            'Sweeny',
            new Address(
                '58 Holwick Close',
                null,
                null,
                null,
                null,
                'DH87UJ'
            ),
            '07939843048',
            'joe@email.com'
        );

        $orderMethod = new OrderMethod(
            FulfilmentType::DELIVERY(),
            new \DateTimeImmutable('2020-03-12T12:00:00+00:00'),
            null
        );

        $orderItems = [
            new OrderItemData(
                Uuid::fromString('4c9dd4ce-f8b0-4a24-b2ea-f29295dc8552'),
                Uuid::fromString( '9af64fc1-6168-4859-99ba-a8173fab472c'),
                100,
                10
            ),
        ];

        $this->assertEquals('ORDER101091', $command->getOrderNumber());
        $this->assertEquals($customer, $command->getCustomer());
        $this->assertEquals($orderMethod, $command->getMethod());
        $this->assertEquals($orderItems, $command->getItems());
    }

    public function test_InvalidArgumentException_is_thrown_if_orderNumber_field_is_missing()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage( "'orderNumber' field is required");
        new CreateOrderCommand(
            '',
            'Joe',
            'Sweeny',
            (object) [],
            '07939843048',
            'joe@email.com',
            (object) [],
            []
        );
    }

    public function test_InvalidArgumentException_is_thrown_if_firstName_field_is_missing()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage( "'firstName' field is required");
        new CreateOrderCommand(
            'ORDER123',
            '',
            'Sweeny',
            (object) [],
            '07939843048',
            'joe@email.com',
            (object) [],
            []
        );
    }

    public function test_InvalidArgumentException_is_thrown_if_lastName_field_is_missing()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage( "'lastName' field is required");
        new CreateOrderCommand(
            'ORDER123',
            'Joe',
            '',
            (object) [],
            '07939843048',
            'joe@email.com',
            (object) [],
            []
        );
    }

    public function test_InvalidArgumentException_is_thrown_if_required_address_field_is_missing()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage( "'address->line1' field is required");
        new CreateOrderCommand(
            'ORDER123',
            'Joe',
            'Sweeny',
            (object) [
                'postCode' => 'DH87UJ',
            ],
            '07939843048',
            'joe@email.com',
            (object) [],
            []
        );
    }

    public function test_InvalidArgumentException_is_thrown_if_phone_field_is_missing()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage( "'phone' field is required");
        new CreateOrderCommand(
            'ORDER123',
            'Joe',
            'Sweeny',
            (object) [
                'line1' => '58 Holwick Close',
                'line2' => 'Templetown',
                'line3' => 'In the ghetto',
                'town' => 'Consett',
                'county' => 'Durham',
                'postCode' => 'DH87UJ',
            ],
            '',
            'joe@email.com',
            (object) [],
            []
        );
    }

    public function test_InvalidArgumentException_is_thrown_if_email_field_is_missing()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage( "'email' field is required");
        new CreateOrderCommand(
            'ORDER123',
            'Joe',
            'Sweeny',
            (object) [
                'line1' => '58 Holwick Close',
                'line2' => 'Templetown',
                'line3' => 'In the ghetto',
                'town' => 'Consett',
                'county' => 'Durham',
                'postCode' => 'DH87UJ',
            ],
            '07939843048',
            '',
            (object) [],
            []
        );
    }

    public function test_InvalidArgumentException_is_thrown_if_required_method_field_is_missing()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage( "'method->type' field is required");
        new CreateOrderCommand(
            'ORDER123',
            'Joe',
            'Sweeny',
            (object) [
                'line1' => '58 Holwick Close',
                'line2' => 'Templetown',
                'line3' => 'In the ghetto',
                'town' => 'Consett',
                'county' => 'Durham',
                'postCode' => 'DH87UJ',
            ],
            '07939843048',
            'joe@email.com',
            (object) [
                'date' => '2020-03-12T12:00:00+00:00',
                'fee' => 250,
            ],
            []
        );
    }

    public function test_InvalidArgumentException_is_thrown_if_required_items_field_is_missing()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid UUID string: ');
        new CreateOrderCommand(
            'ORDER123',
            'Joe',
            'Sweeny',
            (object) [
                'line1' => '58 Holwick Close',
                'line2' => 'Templetown',
                'line3' => 'In the ghetto',
                'town' => 'Consett',
                'county' => 'Durham',
                'postCode' => 'DH87UJ',
            ],
            '07939843048',
            'joe@email.com',
            (object) [
                'type' => 'DELIVERY',
                'date' => '2020-03-12T12:00:00+00:00',
                'fee' => 250,
            ],
            [
                (object) [
                    'priceId' => '9af64fc1-6168-4859-99ba-a8173fab472c',
                    'price' => 100,
                    'quantity' => 10,
                ],
            ]
        );
    }

    public function test_InvalidArgumentException_is_thrown_if_method_data_does_not_match_the_required_schema()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Date provided is not a valid RFC3339 date');
        new CreateOrderCommand(
            'ORDER123',
            'Joe',
            'Sweeny',
            (object) [
                'line1' => '58 Holwick Close',
                'line2' => 'Templetown',
                'line3' => 'In the ghetto',
                'town' => 'Consett',
                'county' => 'Durham',
                'postCode' => 'DH87UJ',
            ],
            '07939843048',
            'joe@email.com',
            (object) [
                'type' => 'DELIVERY',
                'date' => 'Hello',
                'fee' => 250,
            ],
            [
                (object) [
                    'productId' => '4c9dd4ce-f8b0-4a24-b2ea-f29295dc8552',
                    'priceId' => '9af64fc1-6168-4859-99ba-a8173fab472c',
                    'price' => 100,
                    'quantity' => 10,
                ],
            ]
        );
    }
}
