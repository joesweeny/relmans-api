<?php

namespace Relmans\Domain\Persistence\Doctrine;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Relmans\Domain\Entity\Product;
use Relmans\Domain\Entity\ProductPrice;
use Relmans\Domain\Enum\Measurement;
use Relmans\Domain\Enum\ProductStatus;
use Relmans\Domain\Persistence\ProductReader;
use Relmans\Domain\Persistence\ProductReaderQuery;
use Relmans\Framework\Exception\NotFoundException;

class DoctrineProductReader implements ProductReader
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function get(ProductReaderQuery $query): array
    {
        $productBuilder = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('product');

        try {
            $rows = $this->buildQuery($productBuilder, $query)->execute();
        } catch (Exception $e) {
            throw new \RuntimeException("Error executing query: {$e->getMessage()}");
        }

        return array_map(function (array $row) {
            return $this->hydrateProduct((object) $row, $this->hydratePrices($row['id']));
        }, $rows->fetchAllAssociative());
    }

    public function getById(UuidInterface $productId): Product
    {
        $builder =  $this->connection->createQueryBuilder();

        try {
            $row = $builder
                ->select('*')
                ->from('product')
                ->where('id = :id')
                ->setParameter(':id', (string) $productId)
                ->execute()
                ->fetchAssociative();
        } catch (\Exception $e) {
            throw new \RuntimeException("Error executing query: {$e->getMessage()}");
        }

        if (!$row) {
            throw new NotFoundException("Product {$productId} does not exist");
        }

        return $this->hydrateProduct((object) $row, $this->hydratePrices($row['id']));
    }

    public function getPriceById(UuidInterface $priceId): ProductPrice
    {
        $builder =  $this->connection->createQueryBuilder();

        try {
            $row = $builder
                ->select('*')
                ->from('product_price')
                ->where('id = :id')
                ->setParameter(':id', (string) $priceId)
                ->execute()
                ->fetchAssociative();
        } catch (\Exception $e) {
            throw new \RuntimeException("Error executing query: {$e->getMessage()}");
        }

        if (!$row) {
            throw new NotFoundException("Product price {$priceId} does not exist");
        }
        
        return $this->hydratePrice((object) $row);
    }

    private function buildQuery(QueryBuilder $builder, ProductReaderQuery $query): QueryBuilder
    {
        if ($query->getSearchTerm()) {
            $builder
                ->andWhere('LOWER(name) LIKE LOWER(:name)')
                ->setParameter(':name', "%{$query->getSearchTerm()}%");
        }

        if ($query->getCategoryId() !== null) {
            $builder->andWhere('category_id = :category_id')
                ->setParameter(':category_id', (string) $query->getCategoryId());
        }

        if ($query->getOrderBy() === 'name_asc') {
            $builder->orderBy('name', 'ASC');
        }

        if ($query->getOrderBy() === 'name_desc') {
            $builder->orderBy('name', 'DESC');
        }

        return $builder;
    }

    private function hydrateProduct(object $row, array $prices): Product
    {
        return new Product(
            Uuid::fromString($row->id),
            Uuid::fromString($row->category_id),
            $row->name,
            new ProductStatus($row->status),
            (bool) $row->featured,
            $prices,
            \DateTimeImmutable::createFromFormat('U', $row->created_at),
            \DateTimeImmutable::createFromFormat('U', $row->updated_at)
        );
    }

    /**
     * @param string $productId
     * @return array|ProductPrice[]
     */
    private function hydratePrices(string $productId): array
    {
        $priceBuilder = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('product_price');

        try {
            $rows = $priceBuilder
                ->where('product_id = :product_id')
                ->setParameter(':product_id', $productId)
                ->orderBy('measurement', 'ASC')
                ->execute()
                ->fetchAllAssociative();
        } catch (Exception $e) {
            throw new \RuntimeException("Error executing query: {$e->getMessage()}");
        }

        return array_map(function (array $row) {
            return $this->hydratePrice((object) $row);
        }, $rows);
    }

    private function hydratePrice(object $row): ProductPrice
    {
        return new ProductPrice(
            Uuid::fromString($row->id),
            Uuid::fromString($row->product_id),
            $row->value,
            $row->size,
            new Measurement($row->measurement),
            \DateTimeImmutable::createFromFormat('U', $row->created_at),
            \DateTimeImmutable::createFromFormat('U', $row->updated_at)
        );
    }
}
