<?php

namespace Relmans\Domain\Persistence\Doctrine;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Relmans\Domain\Entity\Address;
use Relmans\Domain\Entity\Customer;
use Relmans\Domain\Entity\Order;
use Relmans\Domain\Entity\OrderItem;
use Relmans\Domain\Entity\OrderMethod;
use Relmans\Domain\Enum\FulfilmentType;
use Relmans\Domain\Enum\Measurement;
use Relmans\Domain\Enum\OrderStatus;
use Relmans\Domain\Persistence\OrderWriterQuery;
use Relmans\Framework\Exception\NotFoundException;
use Relmans\Framework\Time\FixedClock;
use Relmans\Traits\RunsMigrations;
use Relmans\Traits\UsesContainer;

class DoctrineOrderWriterTest extends TestCase
{
    use RunsMigrations;
    use UsesContainer;

    private Connection $connection;
    private DoctrineOrderWriter $writer;

    public function setUp(): void
    {
        $container = $this->runMigrations($this->createContainer());
        $this->connection = $container->get(Connection::class);
        $this->writer = new DoctrineOrderWriter(
            $this->connection,
            new FixedClock(new \DateTimeImmutable('2025-02-02T12:00:00+00:00'))
        );
    }

    public function test_insert_increases_order_and_order_item_table_counts()
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
        $status = OrderStatus::ACCEPTED();
        $method = new OrderMethod(FulfilmentType::DELIVERY(), new \DateTimeImmutable(), 250);
        $items = [
            new OrderItem(
                Uuid::uuid4(),
                $orderId,
                Uuid::uuid4(),
                'Cabbage',
                10,
                1,
                Measurement::EACH(),
                100,
                new \DateTimeImmutable(),
                new \DateTimeImmutable(),
            ),
            new OrderItem(
                Uuid::uuid4(),
                $orderId,
                Uuid::uuid4(),
                'Cabbage',
                10,
                1,
                Measurement::EACH(),
                100,
                new \DateTimeImmutable(),
                new \DateTimeImmutable(),
            )
        ];
        $createdAt = new \DateTimeImmutable();
        $updatedAt = new \DateTimeImmutable();

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

        $this->assertEquals(1, $this->tableRowCount('customer_order'));
        $this->assertEquals(2, $this->tableRowCount('customer_order_item'));

        $orderId = '1234567890';
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
        $method = new OrderMethod(FulfilmentType::DELIVERY(), new \DateTimeImmutable(), 250);
        $items = [
            new OrderItem(
                Uuid::uuid4(),
                $orderId,
                Uuid::uuid4(),
                'Cabbage',
                10,
                1,
                Measurement::EACH(),
                100,
                new \DateTimeImmutable(),
                new \DateTimeImmutable(),
            ),
            new OrderItem(
                Uuid::uuid4(),
                $orderId,
                Uuid::uuid4(),
                'Cabbage',
                10,
                1,
                Measurement::EACH(),
                100,
                new \DateTimeImmutable(),
                new \DateTimeImmutable(),
            )
        ];
        $createdAt = new \DateTimeImmutable();
        $updatedAt = new \DateTimeImmutable();

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

        $this->assertEquals(2, $this->tableRowCount('customer_order'));
        $this->assertEquals(4, $this->tableRowCount('customer_order_item'));
    }

    public function test_update_updates_the_status_column_for_an_order_record()
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
        $status = OrderStatus::PENDING();
        $method = new OrderMethod(FulfilmentType::DELIVERY(), new \DateTimeImmutable(), 250);
        $createdAt = new \DateTimeImmutable();
        $updatedAt = new \DateTimeImmutable();

        $order = new Order(
            $orderId,
            $transactionId,
            $customer,
            $status,
            $method,
            [],
            $createdAt,
            $updatedAt
        );

        $this->writer->insert($order);

        $row = $this->fetchRecord($orderId, 'customer_order');

        $this->assertEquals('PENDING', $row->status);

        $query = (new OrderWriterQuery())->setStatus(OrderStatus::ACCEPTED());

        $this->writer->update($orderId, $query);

        $row = $this->fetchRecord($orderId, 'customer_order');

        $this->assertEquals('ACCEPTED', $row->status);
    }

    public function test_order_throws_a_NotFoundException_if_order_record_does_not_exist()
    {
        $orderId = Uuid::uuid4();

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage("Order {$orderId} does not exist");
        $this->writer->update($orderId, new OrderWriterQuery());
    }

    private function tableRowCount(string $table): int
    {
        return $this->connection->createQueryBuilder()
            ->select('*')
            ->from($table)
            ->execute()
            ->rowCount();
    }

    private function fetchRecord(string $id, string $table): object
    {
        return (object) $this->connection->createQueryBuilder()
            ->select('*')
            ->from($table)
            ->where('id = :id')
            ->setParameter(':id', $id)
            ->execute()
            ->fetchAssociative();
    }
}
