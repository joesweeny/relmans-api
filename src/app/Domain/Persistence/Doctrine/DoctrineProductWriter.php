<?php

namespace Relmans\Domain\Persistence\Doctrine;

use Doctrine\DBAL\Connection;
use Ramsey\Uuid\UuidInterface;
use Relmans\Domain\Entity\Product;
use Relmans\Domain\Entity\ProductPrice;
use Relmans\Domain\Enum\ProductStatus;
use Relmans\Domain\Persistence\ProductWriter;
use Relmans\Framework\Exception\NotFoundException;
use Relmans\Framework\Time\Clock;

class DoctrineProductWriter implements ProductWriter
{
    private Connection $connection;
    private Clock $clock;

    public function __construct(Connection $connection, Clock $clock)
    {
        $this->connection = $connection;
        $this->clock = $clock;
    }

    public function insert(Product $product): void
    {
        $query = $this->connection->createQueryBuilder()
            ->insert('product')
            ->values([
                'id' => ':id',
                'category_id' => ':category_id',
                'name' => ':name',
                'status' => ':status',
                'created_at' => ':created_at',
                'updated_at' => ':updated_at',
            ])
            ->setParameter(':id', (string) $product->getId())
            ->setParameter(':category_id', (string) $product->getCategoryId())
            ->setParameter(':name', $product->getName())
            ->setParameter(':status', (string) $product->getStatus())
            ->setParameter(':created_at', $product->getCreatedAt()->getTimestamp())
            ->setParameter(':updated_at', $product->getUpdatedAt()->getTimestamp());

        try {
            $query->execute();
            $this->insertPrices($product->getPrices());
        } catch (\Exception $e) {
            throw new \RuntimeException("Error executing query: {$e->getMessage()}");
        }
    }

    public function updateProductStatus(UuidInterface $id, ProductStatus $status): void
    {
        try {
            $row = $this->connection->createQueryBuilder()
                ->select('1')
                ->from('product')
                ->where('id = :id')
                ->setParameter(':id', (string) $id)
                ->execute()
                ->fetchOne();
        } catch (\Exception $e) {
            throw new \RuntimeException("Error executing query: {$e->getMessage()}");
        }

        if (!$row) {
            throw new NotFoundException("Product {$id} does not exist");
        }

        try {
            $this->connection->createQueryBuilder()
                ->update('product')
                ->set('status', (string) $status)
                ->set('updated_at', $this->clock->now())
                ->where('id = :id')
                ->setParameter(':id', (string) $id)
                ->execute();
        } catch (\Exception $e) {
            throw new \RuntimeException("Error executing query: {$e->getMessage()}");
        }
    }

    public function updateProductPrice(UuidInterface $priceId, int $value): void
    {
        try {
            $row = $this->connection->createQueryBuilder()
                ->select('1')
                ->from('product_price')
                ->where('id = :id')
                ->setParameter(':id', (string) $priceId)
                ->execute()
                ->fetchOne();
        } catch (\Exception $e) {
            throw new \RuntimeException("Error executing query: {$e->getMessage()}");
        }

        if (!$row) {
            throw new NotFoundException("Product price {$priceId} does not exist");
        }

        try {
            $this->connection->createQueryBuilder()
                ->update('product')
                ->set('value', $value)
                ->set('updated_at', $this->clock->now())
                ->where('id = :id')
                ->setParameter(':id', (string) $priceId)
                ->execute();
        } catch (\Exception $e) {
            throw new \RuntimeException("Error executing query: {$e->getMessage()}");
        }
    }

    /**
     * @param array|ProductPrice[] $prices
     * @return void
     */
    private function insertPrices(array $prices): void
    {
        foreach ($prices as $price) {
            $query = $this->connection->createQueryBuilder()
                ->insert('product')
                ->values([
                    'id' => ':id',
                    'product_id' => ':product_id',
                    'value' => ':value',
                    'size' => ':size',
                    'measurement' => ':measurement',
                    'created_at' => ':created_at',
                    'updated_at' => ':updated_at',
                ])
                ->setParameter(':id', (string) $price->getId())
                ->setParameter(':product_id', (string) $price->getProductId())
                ->setParameter(':value', $price->getValue())
                ->setParameter(':size', $price->getSize())
                ->setParameter(':measurement', (string) $price->getMeasurement())
                ->setParameter(':created_at', $price->getCreatedAt()->getTimestamp())
                ->setParameter(':updated_at', $price->getUpdatedAt()->getTimestamp());

            try {
                $query->execute();
            } catch (\Exception $e) {
                throw new \RuntimeException("Error executing query: {$e->getMessage()}");
            }
        }
    }
}
