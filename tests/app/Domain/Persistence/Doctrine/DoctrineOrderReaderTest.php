<?php

namespace Relmans\Domain\Persistence\Doctrine;

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Relmans\Domain\Entity\Address;
use Relmans\Domain\Entity\Customer;
use Relmans\Domain\Entity\Order;
use Relmans\Domain\Entity\OrderItem;
use Relmans\Domain\Entity\OrderMethod;
use Relmans\Domain\Enum\FulfilmentType;
use Relmans\Domain\Enum\Measurement;
use Relmans\Domain\Enum\OrderStatus;
use Relmans\Domain\Persistence\OrderReaderQuery;
use Relmans\Framework\Exception\NotFoundException;
use Relmans\Traits\RunsMigrations;
use Relmans\Traits\UsesContainer;

class DoctrineOrderReaderTest extends TestCase
{
    use RunsMigrations;
    use UsesContainer;

    private DoctrineOrderWriter $writer;
    private DoctrineOrderReader $reader;

    public function setUp(): void
    {
        $container = $this->runMigrations($this->createContainer());
        $this->writer = $container->get(DoctrineOrderWriter::class);
        $this->reader = $container->get(DoctrineOrderReader::class);
    }

    public function test_getById_returns_an_Order_object()
    {
        $this->seedOrders();

        $order = $this->reader->getById('12345678');

        $this->assertEquals('12345678', $order->getId());
        $this->assertEquals('ID9991111', $order->getTransactionId());
        $this->assertEquals('Joe', $order->getCustomer()->getFirstName());
        $this->assertEquals('Sweeny', $order->getCustomer()->getLastName());
        $this->assertEquals('07939843048', $order->getCustomer()->getPhoneNumber());
        $this->assertEquals('58 Holwick Close', $order->getCustomer()->getAddress()->getLine1());
        $this->assertEquals('Templetown', $order->getCustomer()->getAddress()->getLine2());
        $this->assertEquals('In the ghetto', $order->getCustomer()->getAddress()->getLine3());
        $this->assertEquals('Consett', $order->getCustomer()->getAddress()->getTown());
        $this->assertEquals('Durham', $order->getCustomer()->getAddress()->getCounty());
        $this->assertEquals('DH87UJ', $order->getCustomer()->getAddress()->getPostCode());
        $this->assertEquals(OrderStatus::CONFIRMED(), $order->getStatus());
        $this->assertEquals(FulfilmentType::DELIVERY(), $order->getMethod()->getFulfilmentType());
        $this->assertEquals(new \DateTimeImmutable('2021-03-12T11:00:51+00:00'), $order->getMethod()->getDate());
        $this->assertEquals(250, $order->getMethod()->getFee());
        $this->assertEquals(new \DateTimeImmutable('2021-02-23T11:06:51+00:00'), $order->getCreatedAt());
        $this->assertEquals(new \DateTimeImmutable('2021-02-23T11:06:51+00:00'), $order->getUpdatedAt());

        $this->assertEquals(Uuid::fromString('c0c4ad12-f315-4e5e-96ec-97ca9dbd975e'), $order->getItems()[0]->getId());
        $this->assertEquals('12345678', $order->getItems()[0]->getOrderId());
        $this->assertEquals(Uuid::fromString('105804ae-0a60-4a9e-9695-37ae5375dfc4'), $order->getItems()[0]->getProductId());
        $this->assertEquals('Cabbage', $order->getItems()[0]->getName());
        $this->assertEquals(10, $order->getItems()[0]->getPrice());
        $this->assertEquals(1, $order->getItems()[0]->getSize());
        $this->assertEquals(Measurement::EACH(), $order->getItems()[0]->getMeasurement());
        $this->assertEquals(100, $order->getItems()[0]->getQuantity());
        $this->assertEquals(new \DateTimeImmutable('2021-02-23T11:06:51+00:00'), $order->getItems()[0]->getCreatedAt());
        $this->assertEquals(new \DateTimeImmutable('2021-02-23T11:06:51+00:00'), $order->getItems()[0]->getUpdatedAt());

        $this->assertEquals(Uuid::fromString('a7f212ae-8c40-46e1-8b82-dfc4e6f522c5'), $order->getItems()[1]->getId());
        $this->assertEquals('12345678', $order->getItems()[1]->getOrderId());
        $this->assertEquals(Uuid::fromString('1e66340e-b1fd-4950-8e48-c0269f6f9705'), $order->getItems()[1]->getProductId());
        $this->assertEquals('Fennel', $order->getItems()[1]->getName());
        $this->assertEquals(10, $order->getItems()[1]->getPrice());
        $this->assertEquals(1, $order->getItems()[1]->getSize());
        $this->assertEquals(Measurement::EACH(), $order->getItems()[1]->getMeasurement());
        $this->assertEquals(100, $order->getItems()[1]->getQuantity());
        $this->assertEquals(new \DateTimeImmutable('2021-02-23T11:06:51+00:00'), $order->getItems()[1]->getCreatedAt());
        $this->assertEquals(new \DateTimeImmutable('2021-02-23T11:06:51+00:00'), $order->getItems()[1]->getUpdatedAt());
    }

    public function test_getById_throws_a_NotFoundException_if_order_resource_does_not_exist()
    {
        $orderId = Uuid::uuid4();

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage("Order {$orderId} does not exist");
        $this->reader->getById($orderId);
    }

    public function test_get_returns_an_array_of_all_Order_resources()
    {
        $this->seedOrders();

        $fetched = $this->reader->get(new OrderReaderQuery());

        $this->assertCount(2, $fetched);
        $this->assertEquals('12345678', $fetched[0]->getId());
        $this->assertEquals('123455555', $fetched[1]->getId());
    }

    public function test_get_returns_an_array_of_Order_objects_filtered_by_customer_post_code()
    {
        $this->seedOrders();

        $query = (new OrderReaderQuery())->setPostCode('DH87UJ');

        $fetched = $this->reader->get($query);

        $this->assertCount(1, $fetched);
        $this->assertEquals('12345678', $fetched[0]->getId());
    }

    public function test_get_returns_an_array_of_Order_object_filtered_by_delivery_date_from()
    {
        $this->seedOrders();

        $query = (new OrderReaderQuery())->setDeliveryDateFrom(new \DateTimeImmutable('2021-03-12T10:00:51+00:00'));

        $fetched = $this->reader->get($query);

        $this->assertCount(1, $fetched);
        $this->assertEquals('12345678', $fetched[0]->getId());
    }

    public function test_get_returns_an_array_of_Order_object_filtered_by_delivery_date_to()
    {
        $this->seedOrders();

        $query = (new OrderReaderQuery())->setDeliveryDateTo(new \DateTimeImmutable('2021-02-28T10:00:51+00:00'));

        $fetched = $this->reader->get($query);

        $this->assertCount(1, $fetched);
        $this->assertEquals('123455555', $fetched[0]->getId());
    }

    public function test_get_returns_an_array_of_Order_object_filtered_by_order_date_from()
    {
        $this->seedOrders();

        $query = (new OrderReaderQuery())->setOrderDateFrom(new \DateTimeImmutable('2021-05-23T11:00:51+00:00'));

        $fetched = $this->reader->get($query);

        $this->assertCount(1, $fetched);
        $this->assertEquals('123455555', $fetched[0]->getId());
    }

    public function test_get_returns_an_array_of_Order_object_filtered_by_order_date_to()
    {
        $this->seedOrders();

        $query = (new OrderReaderQuery())->setOrderDateTo(new \DateTimeImmutable('2021-04-23T11:00:51+00:00'));

        $fetched = $this->reader->get($query);

        $this->assertCount(1, $fetched);
        $this->assertEquals('12345678', $fetched[0]->getId());
    }

    public function test_get_returns_an_array_of_Order_objects_ordered_by_created_at_date_asc()
    {
        $this->seedOrders();

        $query = (new OrderReaderQuery())->setOrderBy('created_at_asc');

        $fetched = $this->reader->get($query);

        $this->assertCount(2, $fetched);
        $this->assertEquals('12345678', $fetched[0]->getId());
        $this->assertEquals('123455555', $fetched[1]->getId());
    }

    public function test_get_returns_an_array_of_Order_objects_ordered_by_created_at_date_desc()
    {
        $this->seedOrders();

        $query = (new OrderReaderQuery())->setOrderBy('created_at_desc');

        $fetched = $this->reader->get($query);

        $this->assertCount(2, $fetched);
        $this->assertEquals('123455555', $fetched[0]->getId());
        $this->assertEquals('12345678', $fetched[1]->getId());
    }

    private function seedOrders(): void
    {
        $orderId = '12345678';
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
            new \DateTimeImmutable('2021-03-12T11:00:51+00:00'),
            250
        );
        $items = [
            new OrderItem(
                Uuid::fromString('c0c4ad12-f315-4e5e-96ec-97ca9dbd975e'),
                $orderId,
                Uuid::fromString('105804ae-0a60-4a9e-9695-37ae5375dfc4'),
                'Cabbage',
                10,
                1,
                Measurement::EACH(),
                100,
                new \DateTimeImmutable('2021-02-23T11:06:51+00:00'),
                new \DateTimeImmutable('2021-02-23T11:06:51+00:00'),
            ),
            new OrderItem(
                Uuid::fromString('a7f212ae-8c40-46e1-8b82-dfc4e6f522c5'),
                $orderId,
                Uuid::fromString('1e66340e-b1fd-4950-8e48-c0269f6f9705'),
                'Fennel',
                10,
                1,
                Measurement::EACH(),
                100,
                new \DateTimeImmutable('2021-02-23T11:06:51+00:00'),
                new \DateTimeImmutable('2021-02-23T11:06:51+00:00'),
            )
        ];
        $createdAt = new \DateTimeImmutable('2021-02-23T11:06:51+00:00');
        $updatedAt = new \DateTimeImmutable('2021-02-23T11:06:51+00:00');

        $order = new Order(
            $orderId,
            $transactionId,
            $customer,
            $status,
            $method,
            $items,
            $createdAt,
            $updatedAt
        );

        $this->writer->insert($order);

        $orderId = '123455555';
        $transactionId = 'ID9991111';
        $address = new Address(
            '35 Beechfield Gardens',
            'Crow Lane',
            'Edge of Dagenham',
            'Romford',
            'Essex',
            'RM7 0EJ'
        );
        $customer = new Customer(
            'Joe',
            'Sweeny',
            $address,
            '07939843048',
            'joe@email.com'
        );
        $status = OrderStatus::CONFIRMED();
        $method = new OrderMethod(FulfilmentType::DELIVERY(), new \DateTimeImmutable('2021-02-23T11:06:51+00:00'), 250);
        $items = [
            new OrderItem(
                Uuid::fromString('a270e34b-4c89-4727-a9ff-c96991eaedff'),
                $orderId,
                Uuid::fromString('0b9fa25f-c5da-4761-a1f6-f926aaca1dd7'),
                'Cabbage',
                25,
                1,
                Measurement::EACH(),
                100,
                new \DateTimeImmutable('2021-02-23T11:06:51+00:00'),
                new \DateTimeImmutable('2021-02-23T11:06:51+00:00'),
            ),
        ];
        $createdAt = new \DateTimeImmutable('2021-05-23T11:06:51+00:00');
        $updatedAt = new \DateTimeImmutable('2021-05-23T11:06:51+00:00');

        $order = new Order(
            $orderId,
            $transactionId,
            $customer,
            $status,
            $method,
            $items,
            $createdAt,
            $updatedAt
        );

        $this->writer->insert($order);
    }
}
