<?php

namespace Relmans\Domain\Persistence\Doctrine;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;
use Ramsey\Uuid\UuidInterface;
use Relmans\Domain\Entity\Order;
use Relmans\Domain\Persistence\OrderReader;
use Relmans\Domain\Persistence\OrderReaderQuery;
use Relmans\Framework\Exception\NotFoundException;

class DoctrineOrderReader implements OrderReader
{
    private Connection $connection;
    private OrderHydrator $hydrator;

    public function __construct(Connection $connection, OrderHydrator $hydrator)
    {
        $this->connection = $connection;
        $this->hydrator = $hydrator;
    }

    public function getById(string $orderId): Order
    {
        $builder =  $this->connection->createQueryBuilder();

        try {
            $row = $builder
                ->select('*')
                ->from('customer_order')
                ->where('id = :id')
                ->setParameter(':id', $orderId)
                ->execute()
                ->fetchAssociative();
        } catch (\Exception $e) {
            throw new \RuntimeException("Error executing query: {$e->getMessage()}");
        }

        if (!$row) {
            throw new NotFoundException("Order {$orderId} does not exist");
        }

        return $this->hydrator->hydrateOrder((object) $row, $this->fetchOrderItems($row['id']));
    }

    public function get(OrderReaderQuery $query): array
    {
        $builder = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('customer_order');

        try {
            $rows = $this->buildQuery($builder, $query)->execute();
        } catch (Exception $e) {
            throw new \RuntimeException("Error executing query: {$e->getMessage()}");
        }

        return array_map(function (array $row) {
            return $this->hydrator->hydrateOrder((object) $row, $this->fetchOrderItems($row['id']));
        }, $rows->fetchAllAssociative());
    }

    /**
     * @param string $orderId
     * @return array|array[]
     */
    private function fetchOrderItems(string $orderId): array
    {
        $builder = $this->connection->createQueryBuilder();

        try {
            $fetched = $builder
                ->select('*')
                ->from('customer_order_item')
                ->where('order_id = :order_id')
                ->setParameter(':order_id', (string) $orderId)
                ->execute()
                ->fetchAllAssociative();
        } catch (Exception $e) {
            throw new \RuntimeException("Error executing query: {$e->getMessage()}");
        }

        return $fetched;
    }

    private function buildQuery(QueryBuilder $builder, OrderReaderQuery $query): QueryBuilder
    {
        if ($query->getPostCode() !== null) {
            $builder
                ->andWhere("customer_details->'address'->>'postCode' = :post_code")
                ->setParameter(':post_code', strtoupper($query->getPostCode()));
        }

        if ($query->getDeliveryDateFrom() !== null) {
            $builder
                ->andWhere("method->>'date' >= :delivery_from")
                ->setParameter(':delivery_from', $query->getDeliveryDateFrom()->getTimestamp());
        }

        if ($query->getDeliveryDateTo() !== null) {
            $builder
                ->andWhere("method->>'date' <= :delivery_to")
                ->setParameter(':delivery_to', $query->getDeliveryDateTo()->getTimestamp());
        }

        if ($query->getOrderDateFrom() !== null) {
            $builder
                ->andWhere('created_at >= :created_at_from')
                ->setParameter(':created_at_from', $query->getOrderDateFrom()->getTimestamp());
        }

        if ($query->getOrderDateTo() !== null) {
            $builder
                ->andWhere('created_at <= :created_at_to')
                ->setParameter(':created_at_to', $query->getOrderDateTo()->getTimestamp());
        }

        if ($query->getOrderBy() !== null) {
            if ($query->getOrderBy() === 'created_at_asc') {
                $builder->orderBy('created_at', 'ASC');
            }

            if ($query->getOrderBy() === 'created_at_desc') {
                $builder->orderBy('created_at', 'DESC');
            }
        }

        if ($query->getStatus() !== null) {
            $builder->andWhere('status = :status')
                ->setParameter(':status', $query->getStatus()->getValue());
        }

        return $builder;
    }
}
