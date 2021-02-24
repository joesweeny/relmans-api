<?php

namespace Relmans\Domain\Persistence\Doctrine;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types;
use Ramsey\Uuid\UuidInterface;
use Relmans\Domain\Entity\Order;
use Relmans\Domain\Entity\OrderItem;
use Relmans\Domain\Persistence\OrderWriter;
use Relmans\Domain\Persistence\OrderWriterQuery;
use Relmans\Framework\Exception\NotFoundException;
use Relmans\Framework\Time\Clock;

class DoctrineOrderWriter implements OrderWriter
{
    private Connection $connection;
    private Clock $clock;

    public function __construct(Connection $connection, Clock $clock)
    {
        $this->connection = $connection;
        $this->clock = $clock;
    }

    public function insert(Order $order): void
    {
        $builder = $this->connection->createQueryBuilder();

        $query = $builder
            ->insert("customer_order")
            ->values([
                'id' => ':id',
                'transaction_id' => ':transaction_id',
                'customer_details' => ':customer_details',
                'status' => ':status',
                'method' => ':method',
                'created_at' => ':created_at',
                'updated_at' => ':updated_at',
            ])
            ->setParameter(':id', $order->getId())
            ->setParameter(':transaction_id', $order->getTransactionId())
            ->setParameter(':customer_details', $order->getCustomer()->jsonSerialize(), Types::JSON)
            ->setParameter(':status', (string) $order->getStatus())
            ->setParameter(':method', $order->getMethod()->jsonSerialize(), Types::JSON)
            ->setParameter(':created_at', $order->getCreatedAt()->getTimestamp())
            ->setParameter(':updated_at', $order->getUpdatedAt()->getTimestamp());

        try {
            $query->execute();
            $this->insertOrderItems($order->getItems());
        } catch (\Exception $e) {
            throw new \RuntimeException("Error executing query: {$e->getMessage()}");
        }
    }

    public function update(string $orderId, OrderWriterQuery $query): void
    {
        $builder =  $this->connection->createQueryBuilder();

        try {
            $row = $builder
                ->select('1')
                ->from('customer_order')
                ->where('id = :id')
                ->setParameter(':id', $orderId)
                ->execute()
                ->fetchOne();
        } catch (\Exception $e) {
            throw new \RuntimeException("Error executing query: {$e->getMessage()}");
        }

        if (!$row) {
            throw new NotFoundException("Order {$orderId} does not exist");
        }

        $builder = $builder
            ->update('customer_order')
            ->set('updated_at', $this->clock->now()->getTimestamp())
            ->where('id = :id')
            ->setParameter(':id', (string) $orderId);

        if ($query->getStatus() !== null) {
            $builder->set('status', $builder->createNamedParameter((string) $query->getStatus()));
        }

        try {
            $builder->execute();
        } catch (\Exception $e) {
            throw new \RuntimeException("Error executing query: {$e->getMessage()}");
        }
    }

    /**
     * @param array|OrderItem[] $items
     * @return void
     */
    private function insertOrderItems(array $items): void
    {
        foreach ($items as $item) {
            $query = $this->connection->createQueryBuilder()
                ->insert('customer_order_item')
                ->values([
                    'id' => ':id',
                    'order_id' => ':order_id',
                    'product_id' => ':product_id',
                    'name' => ':name',
                    'price' => ':price',
                    'size' => ':size',
                    'measurement' => ':measurement',
                    'quantity' => ':quantity',
                    'created_at' => ':created_at',
                    'updated_at' => ':updated_at',
                ])
                ->setParameter(':id', (string) $item->getId())
                ->setParameter(':order_id', (string) $item->getOrderId())
                ->setParameter(':product_id', (string) $item->getProductId())
                ->setParameter(':name', $item->getName())
                ->setParameter(':price', $item->getPrice())
                ->setParameter(':size', $item->getSize())
                ->setParameter(':measurement', (string) $item->getMeasurement())
                ->setParameter(':quantity', $item->getQuantity())
                ->setParameter(':created_at', $item->getCreatedAt()->getTimestamp())
                ->setParameter(':updated_at', $item->getUpdatedAt()->getTimestamp());

            try {
                $query->execute();
            } catch (\Exception $e) {
                throw new \RuntimeException("Error executing query: {$e->getMessage()}");
            }
        }
    }
}
