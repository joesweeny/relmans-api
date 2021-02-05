<?php

namespace Relmans\Domain\Persistence\Doctrine;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Relmans\Domain\Entity\Product;
use Relmans\Domain\Entity\ProductPrice;
use Relmans\Domain\Enum\Measurement;
use Relmans\Domain\Enum\ProductStatus;
use Relmans\Domain\Persistence\ProductReaderQuery;
use Relmans\Traits\RunsMigrations;
use Relmans\Traits\UsesContainer;

class DoctrineProductReaderTest extends TestCase
{
    use RunsMigrations;
    use UsesContainer;

    private DoctrineProductWriter $writer;
    private DoctrineProductReader $reader;

    public function setUp(): void
    {
        $container = $this->runMigrations($this->createContainer());
        $connection = $container->get(Connection::class);
        $this->writer = $container->get(DoctrineProductWriter::class);
        $this->reader = new DoctrineProductReader($connection);
    }

    public function test_get_returns_an_array_of_Product_objects()
    {
        $this->seedProducts();

        $fetched = $this->reader->get(new ProductReaderQuery());

        $this->assertEquals($this->products(), $fetched);
    }

    public function test_get_returns_an_array_of_ordered_products_by_name()
    {
        $this->seedProducts();

        $query = (new ProductReaderQuery())->setOrderBy('name_desc');

        $fetched = $this->reader->get($query);

        $this->assertCount(2, $fetched);
        $this->assertEquals(Uuid::fromString('951af4b9-ce31-4ec9-bb00-fa34b6ed06a8'), $fetched[0]->getId());
        $this->assertEquals('Vegetables', $fetched[0]->getName());
        $this->assertEquals(Uuid::fromString('ea00060d-fb4a-4583-a76c-736f0c06bd86'), $fetched[1]->getId());
        $this->assertEquals('Fruit', $fetched[1]->getName());
    }

    public function test_get_returns_an_array_of_objects_filtered_by_category_id()
    {
        $this->seedProducts();

        $query = (new ProductReaderQuery())
            ->setCategoryId(Uuid::fromString('c4f8fa24-4d63-4dd5-aa90-227aeda9d865'));

        $fetched = $this->reader->get($query);

        $this->assertCount(1, $fetched);
        $this->assertEquals(Uuid::fromString('ea00060d-fb4a-4583-a76c-736f0c06bd86'), $fetched[0]->getId());
    }

    public function test_get_returns_an_array_of_object_filtered_by_name()
    {
        $this->seedProducts();

        $query = (new ProductReaderQuery())->setSearchTerm('veg');

        $fetched = $this->reader->get($query);

        $this->assertCount(1, $fetched);
        $this->assertEquals(Uuid::fromString('951af4b9-ce31-4ec9-bb00-fa34b6ed06a8'), $fetched[0]->getId());
    }

    private function seedProducts(): void
    {
        foreach ($this->products() as $product) {
            $this->writer->insert($product);
        }
    }

    private function products(): array
    {
        $product1Id = Uuid::fromString('ea00060d-fb4a-4583-a76c-736f0c06bd86');

        $prices1 = [
            new ProductPrice(
                Uuid::fromString('f09f19b8-8e1f-450d-86cc-2930ec3fa10f'),
                $product1Id,
                1000,
                1.5,
                Measurement::KILOGRAMS(),
                new \DateTimeImmutable('2025-02-02T12:00:00+00:00'),
                new \DateTimeImmutable('2025-02-02T12:00:00+00:00')
            ),
            new ProductPrice(
                Uuid::fromString('eb3553bf-4e93-4a76-a9e2-85c37fe9d957'),
                $product1Id,
                1000,
                500,
                Measurement::GRAMS(),
                new \DateTimeImmutable('2025-02-02T12:00:00+00:00'),
                new \DateTimeImmutable('2025-02-02T12:00:00+00:00')
            )
        ];

        $product1 = new Product(
            $product1Id,
            Uuid::fromString('c4f8fa24-4d63-4dd5-aa90-227aeda9d865'),
            'Fruit',
            ProductStatus::IN_STOCK(),
            $prices1,
            new \DateTimeImmutable('2025-02-02T12:00:00+00:00'),
            new \DateTimeImmutable('2025-02-02T12:00:00+00:00')
        );

        $product2Id = Uuid::fromString('951af4b9-ce31-4ec9-bb00-fa34b6ed06a8');

        $prices2 = [
            new ProductPrice(
                Uuid::fromString('1b1a8c0e-c3a8-49fd-b2fc-e67230868a64'),
                $product2Id,
                1000,
                1.5,
                Measurement::KILOGRAMS(),
                new \DateTimeImmutable('2025-02-02T12:00:00+00:00'),
                new \DateTimeImmutable('2025-02-02T12:00:00+00:00')
            ),
            new ProductPrice(
                Uuid::fromString('c75a1d8f-c226-4d24-a532-355173811379'),
                $product2Id,
                1000,
                500,
                Measurement::GRAMS(),
                new \DateTimeImmutable('2025-02-02T12:00:00+00:00'),
                new \DateTimeImmutable('2025-02-02T12:00:00+00:00')
            )
        ];

        $product2 = new Product(
            $product2Id,
            Uuid::fromString('fbf48e7e-b48b-4589-8b93-e72c9b0fe7ef'),
            'Vegetables',
            ProductStatus::IN_STOCK(),
            $prices2,
            new \DateTimeImmutable('2025-02-02T12:00:00+00:00'),
            new \DateTimeImmutable('2025-02-02T12:00:00+00:00')
        );

        return [
            $product1,
            $product2,
        ];
    }
}
