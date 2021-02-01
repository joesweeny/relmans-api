<?php

namespace Relmans\Domain\Persistence\Doctrine;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Relmans\Domain\Entity\Category;
use Relmans\Traits\RunsMigrations;
use Relmans\Traits\UsesContainer;

class DoctrineCategoryRepositoryTest extends TestCase
{
    use RunsMigrations;
    Use UsesContainer;

    private Connection $connection;
    private DoctrineCategoryRepository $repository;

    public function setUp(): void
    {
        $container = $this->runMigrations($this->createContainer());
        $this->connection = $container->get(Connection::class);
        $this->repository = new DoctrineCategoryRepository($this->connection);
    }

    public function test_insert_increases_table_count()
    {
        $category1 = new Category(Uuid::uuid4(), 'Fruit', new \DateTimeImmutable(), new \DateTimeImmutable());

        $this->repository->insert($category1);

        $this->assertEquals(1, $this->tableRowCount());

        $category2 = new Category(Uuid::uuid4(), 'Vegetables', new \DateTimeImmutable(), new \DateTimeImmutable());

        $this->repository->insert($category2);

        $this->assertEquals(2, $this->tableRowCount());
    }

    public function test_get_returns_an_array_of_Category_objects()
    {
        $category1 = new Category(
            Uuid::fromString('bccd0a06-605c-43ad-bd6d-c79e6e5202f0'),
            'Fruit',
            new \DateTimeImmutable('2021-03-12T12:00:00+00:00'),
            new \DateTimeImmutable('2021-03-12T12:00:00+00:00')
        );

        $category2 = new Category(
            Uuid::fromString('6e5dcdf4-e8a7-4ef6-9cbe-c2eff8ad7eff'),
            'Vegetables',
            new \DateTimeImmutable('2021-03-12T12:00:00+00:00'),
            new \DateTimeImmutable('2021-03-12T12:00:00+00:00')
        );

        $this->repository->insert($category1);
        $this->repository->insert($category2);

        $fetched = $this->repository->get();

        $this->assertEquals($category1, $fetched[0]);
        $this->assertEquals($category2, $fetched[1]);
    }

    private function tableRowCount(): int
    {
        return $this->connection->createQueryBuilder()
            ->select('*')
            ->from('category')
            ->execute()
            ->rowCount();
    }
}
