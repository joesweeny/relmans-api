<?php

namespace Relmans\Domain\Persistence\Doctrine;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Ramsey\Uuid\Uuid;
use Relmans\Domain\Entity\Category;
use Relmans\Domain\Persistence\CategoryRepository;

class DoctrineCategoryRepository implements CategoryRepository
{
    /**
     * @var Connection
     */
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function insert(Category $category): void
    {
        $query = $this->connection->createQueryBuilder()
            ->insert('category')
            ->values([
                'id' => ':id',
                'name' => ':name',
                'created_at' => ':created_at',
                'updated_at' => ':updated_at',
            ])
            ->setParameter(':id', (string) $category->getId())
            ->setParameter(':name', $category->getName())
            ->setParameter(':created_at', $category->getCreatedAt()->getTimestamp())
            ->setParameter(':updated_at', $category->getUpdatedAt()->getTimestamp());

        try {
            $query->execute();
        } catch (\Exception $e) {
            throw new \RuntimeException("Error executing query: {$e->getMessage()}");
        }
    }

    public function get(): array
    {
        $query = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('category');

        try {
            $rows = $query->execute();
        } catch (Exception $e) {
            throw new \RuntimeException("Error executing query: {$e->getMessage()}");
        }

        return array_map(function ($row) {
            return $this->hydrateRow((object) $row);
        }, $rows->fetchAllAssociative());
    }

    private function hydrateRow(object $row): Category
    {
        return new Category(
            Uuid::fromString($row->id),
            $row->name,
            \DateTimeImmutable::createFromFormat('U', $row->created_at),
            \DateTimeImmutable::createFromFormat('U', $row->updated_at)
        );
    }
}
