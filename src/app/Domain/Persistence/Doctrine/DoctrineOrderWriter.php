<?php

namespace Relmans\Domain\Persistence\Doctrine;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types;
use Relmans\Domain\Entity\Order;
use Relmans\Domain\Entity\OrderItem;
use Relmans\Domain\Persistence\OrderWriter;
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
        $query = $this->connection->createQueryBuilder()
            ->insert("customer_order")
            ->values([
                'id' => ':id',
                'external_id' => ':external_id',
                'transaction_id' => ':transaction_id',
                'customer_details' => ':customer_details',
                'status' => ':status',
                'method' => ':method',
                'created_at' => ':created_at',
                'updated_at' => ':updated_at',
            ])
            ->setParameter(':id', (string) $order->getId())
            ->setParameter(':external_id', $order->getExternalId())
            ->setParameter(':transaction_id', $order->getTransactionId())
            ->setParameter(':customer_details', json_encode($order->getCustomer()), Types::JSON)
            ->setParameter(':status', (string) $order->getStatus())
            ->setParameter(':method', json_encode($order->getMethod()), Types::JSON)
            ->setParameter(':created_at', $order->getCreatedAt()->getTimestamp())
            ->setParameter(':updated_at', $order->getUpdatedAt()->getTimestamp());

        try {
            $query->execute();
            $this->insertOrderItems($order->getItems());
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
                ->setParameter(':measurement', $item->getMeasurement())
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