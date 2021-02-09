<?php

namespace Relmans\Domain\Persistence\Doctrine;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Query\QueryBuilder;
use Ramsey\Uuid\Uuid;
use Relmans\Domain\Entity\Product;
use Relmans\Domain\Entity\ProductPrice;
use Relmans\Domain\Enum\Measurement;
use Relmans\Domain\Enum\ProductStatus;
use Relmans\Domain\Persistence\ProductReader;
use Relmans\Domain\Persistence\ProductReaderQuery;

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

        $priceBuilder = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('product_price');

        try {
            $rows = $this->buildQuery($productBuilder, $query)->execute();
        } catch (Exception $e) {
            throw new \RuntimeException("Error executing query: {$e->getMessage()}");
        }

        return array_map(function (array $row) use ($priceBuilder) {
            try {
                $rows = $priceBuilder
                    ->where('product_id = :product_id')
                    ->setParameter(':product_id', $row['id'])
                    ->orderBy('measurement', 'ASC')
                    ->execute()
                    ->fetchAllAssociative();
            } catch (Exception $e) {
                throw new \RuntimeException("Error executing query: {$e->getMessage()}");
            }

            $prices = array_map(function (array $price) {
                return $this->hydratePrice((object) $price);
            }, $rows);

                return $this->hydrateProduct((object) $row, $prices);
        }, $rows->fetchAllAssociative());
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
