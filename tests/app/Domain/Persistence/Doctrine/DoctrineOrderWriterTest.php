<?php

namespace app\Domain\Persistence\Doctrine;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Relmans\Domain\Entity\Address;
use Relmans\Domain\Entity\Customer;
use Relmans\Domain\Entity\Order;
use Relmans\Domain\Entity\OrderItem;
use Relmans\Domain\Entity\OrderMethod;
use Relmans\Domain\Enum\FulfilmentType;
use Relmans\Domain\Enum\OrderStatus;
use Relmans\Domain\Persistence\Doctrine\DoctrineOrderWriter;
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
        $orderId = Uuid::uuid4();
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
                Uuid::uuid4(),
                $orderId,
                Uuid::uuid4(),
                'Cabbage',
                10,
                1,
                'each',
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
                'each',
                100,
                new \DateTimeImmutable(),
                new \DateTimeImmutable(),
            )
        ];
        $createdAt = new \DateTimeImmutable();
        $updatedAt = new \DateTimeImmutable();

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

        $this->assertEquals(1, $this->tableRowCount('customer_order'));
        $this->assertEquals(2, $this->tableRowCount('customer_order_item'));

        $orderId = Uuid::uuid4();
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
                Uuid::uuid4(),
                $orderId,
                Uuid::uuid4(),
                'Cabbage',
                10,
                1,
                'each',
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
                'each',
                100,
                new \DateTimeImmutable(),
                new \DateTimeImmutable(),
            )
        ];
        $createdAt = new \DateTimeImmutable();
        $updatedAt = new \DateTimeImmutable();

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

        $this->assertEquals(2, $this->tableRowCount('customer_order'));
        $this->assertEquals(4, $this->tableRowCount('customer_order_item'));
    }

    private function tableRowCount(string $table): int
    {
        return $this->connection->createQueryBuilder()
            ->select('*')
            ->from($table)
            ->execute()
            ->rowCount();
    }
}
