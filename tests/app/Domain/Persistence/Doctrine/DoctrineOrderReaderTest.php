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

        $order = $this->reader->getById(Uuid::fromString('ea00060d-fb4a-4583-a76c-736f0c06bd86'));

        $this->assertEquals(Uuid::fromString('ea00060d-fb4a-4583-a76c-736f0c06bd86'), $order->getId());
        $this->assertEquals('12345678', $order->getExternalId());
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
        $this->assertEquals(new \DateTimeImmutable('2021-02-23T11:06:51+00:00'), $order->getMethod()->getDate());
        $this->assertEquals(250, $order->getMethod()->getFee());
        $this->assertEquals(new \DateTimeImmutable('2021-02-23T11:06:51+00:00'), $order->getCreatedAt());
        $this->assertEquals(new \DateTimeImmutable('2021-02-23T11:06:51+00:00'), $order->getUpdatedAt());

        $this->assertEquals(Uuid::fromString('c0c4ad12-f315-4e5e-96ec-97ca9dbd975e'), $order->getItems()[0]->getId());
        $this->assertEquals(Uuid::fromString('ea00060d-fb4a-4583-a76c-736f0c06bd86'), $order->getItems()[0]->getOrderId());
        $this->assertEquals(Uuid::fromString('105804ae-0a60-4a9e-9695-37ae5375dfc4'), $order->getItems()[0]->getProductId());
        $this->assertEquals('Cabbage', $order->getItems()[0]->getName());
        $this->assertEquals(10, $order->getItems()[0]->getPrice());
        $this->assertEquals(1, $order->getItems()[0]->getSize());
        $this->assertEquals(Measurement::EACH(), $order->getItems()[0]->getMeasurement());
        $this->assertEquals(100, $order->getItems()[0]->getQuantity());
        $this->assertEquals(new \DateTimeImmutable('2021-02-23T11:06:51+00:00'), $order->getItems()[0]->getCreatedAt());
        $this->assertEquals(new \DateTimeImmutable('2021-02-23T11:06:51+00:00'), $order->getItems()[0]->getUpdatedAt());

        $this->assertEquals(Uuid::fromString('a7f212ae-8c40-46e1-8b82-dfc4e6f522c5'), $order->getItems()[1]->getId());
        $this->assertEquals(Uuid::fromString('ea00060d-fb4a-4583-a76c-736f0c06bd86'), $order->getItems()[1]->getOrderId());
        $this->assertEquals(Uuid::fromString('1e66340e-b1fd-4950-8e48-c0269f6f9705'), $order->getItems()[1]->getProductId());
        $this->assertEquals('Fennel', $order->getItems()[1]->getName());
        $this->assertEquals(10, $order->getItems()[1]->getPrice());
        $this->assertEquals(1, $order->getItems()[1]->getSize());
        $this->assertEquals(Measurement::EACH(), $order->getItems()[1]->getMeasurement());
        $this->assertEquals(100, $order->getItems()[1]->getQuantity());
        $this->assertEquals(new \DateTimeImmutable('2021-02-23T11:06:51+00:00'), $order->getItems()[1]->getCreatedAt());
        $this->assertEquals(new \DateTimeImmutable('2021-02-23T11:06:51+00:00'), $order->getItems()[1]->getUpdatedAt());
    }

    private function seedOrders(): void
    {
        $orderId = Uuid::fromString('ea00060d-fb4a-4583-a76c-736f0c06bd86');
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
        $method = new OrderMethod(
            FulfilmentType::DELIVERY(),
            new \DateTimeImmutable('2021-02-23T11:06:51+00:00'),
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
            $externalId,
            $transactionId,
            $customer,
            $status,
            $method,
            $items,
            $createdAt,
            $updatedAt
        );

        $this->writer->insert($order);

        $orderId = Uuid::fromString('c9a16e3d-d1c8-4e93-8412-ff0d6a9c44a7');
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
        $method = new OrderMethod(FulfilmentType::DELIVERY(), new \DateTimeImmutable(), 250);
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
        $createdAt = new \DateTimeImmutable('2021-02-23T11:06:51+00:00');
        $updatedAt = new \DateTimeImmutable('2021-02-23T11:06:51+00:00');

        $order = new Order(
            $orderId,
            $externalId,
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
