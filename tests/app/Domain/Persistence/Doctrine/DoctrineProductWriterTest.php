<?php

namespace Relmans\Domain\Persistence\Doctrine;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Relmans\Domain\Entity\Product;
use Relmans\Domain\Entity\ProductPrice;
use Relmans\Domain\Enum\Measurement;
use Relmans\Domain\Enum\ProductStatus;
use Relmans\Framework\Exception\NotFoundException;
use Relmans\Framework\Time\FixedClock;
use Relmans\Traits\RunsMigrations;
use Relmans\Traits\UsesContainer;

class DoctrineProductWriterTest extends TestCase
{
    use RunsMigrations;
    use UsesContainer;

    private Connection $connection;
    private DoctrineProductWriter $writer;

    public function setUp(): void
    {
        $container = $this->runMigrations($this->createContainer());
        $this->connection = $container->get(Connection::class);
        $this->writer = new DoctrineProductWriter(
            $this->connection,
            new FixedClock(new \DateTimeImmutable('2025-02-02T12:00:00+00:00'))
        );
    }

    public function test_insert_increases_product_table_and_produce_prices_table_counts()
    {
        $productId = Uuid::uuid4();

        $prices = [
            new ProductPrice(
                Uuid::uuid4(),
                $productId,
                1000,
                1.5,
                Measurement::KILOGRAMS(),
                new \DateTimeImmutable(),
                new \DateTimeImmutable()
            ),
            new ProductPrice(
                Uuid::uuid4(),
                Uuid::uuid4(),
                1000,
                500,
                Measurement::GRAMS(),
                new \DateTimeImmutable(),
                new \DateTimeImmutable()
            )
        ];

        $product = new Product(
            $productId,
            Uuid::uuid4(),
            'Fruit',
            ProductStatus::IN_STOCK(),
            $prices,
            new \DateTimeImmutable(),
            new \DateTimeImmutable()
        );

        $this->writer->insert($product);

        $this->assertEquals(1, $this->tableRowCount('product'));
        $this->assertEquals(2, $this->tableRowCount('product_price'));

        $prices = [
            new ProductPrice(
                Uuid::uuid4(),
                $productId,
                1000,
                1.5,
                Measurement::KILOGRAMS(),
                new \DateTimeImmutable(),
                new \DateTimeImmutable()
            ),
            new ProductPrice(
                Uuid::uuid4(),
                Uuid::uuid4(),
                1000,
                500,
                Measurement::GRAMS(),
                new \DateTimeImmutable(),
                new \DateTimeImmutable()
            )
        ];

        $product = new Product(
            Uuid::uuid4(),
            Uuid::uuid4(),
            'Fruit',
            ProductStatus::OUT_OF_SEASON(),
            $prices,
            new \DateTimeImmutable(),
            new \DateTimeImmutable()
        );

        $this->writer->insert($product);

        $this->assertEquals(2, $this->tableRowCount('product'));
        $this->assertEquals(4, $this->tableRowCount('product_price'));
    }

    public function test_updateProductStatus_updates_the_status_column_for_a_product_record()
    {
        $productId = Uuid::uuid4();

        $prices = [
            new ProductPrice(
                Uuid::uuid4(),
                $productId,
                1000,
                1.5,
                Measurement::KILOGRAMS(),
                new \DateTimeImmutable(),
                new \DateTimeImmutable()
            ),
        ];

        $product = new Product(
            $productId,
            Uuid::uuid4(),
            'Fruit',
            ProductStatus::OUT_OF_SEASON(),
            $prices,
            new \DateTimeImmutable(),
            new \DateTimeImmutable()
        );

        $this->writer->insert($product);

        $row = $this->fetchRecord($productId, 'product');

        $this->assertEquals('OUT_OF_SEASON', $row->status);

        $this->writer->updateProductStatus($productId, ProductStatus::IN_STOCK());

        $row = $this->fetchRecord($productId, 'product');

        $this->assertEquals('IN_STOCK', $row->status);
        $this->assertEquals(1738497600, $row->updated_at);
    }

    public function test_updateProductStatus_throws_a_NotFoundException_if_product_does_not_exist()
    {
        $productId = Uuid::uuid4();

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage("Product {$productId} does not exist");
        $this->writer->updateProductStatus($productId, ProductStatus::IN_STOCK());
    }

    public function test_updateProductPrice_updates_the_value_column_for_a_product_price_record()
    {
        $productId = Uuid::uuid4();
        $priceId = Uuid::uuid4();

        $prices = [
            new ProductPrice(
                $priceId,
                $productId,
                1000,
                1.5,
                Measurement::KILOGRAMS(),
                new \DateTimeImmutable(),
                new \DateTimeImmutable()
            ),
        ];

        $product = new Product(
            $productId,
            Uuid::uuid4(),
            'Fruit',
            ProductStatus::OUT_OF_SEASON(),
            $prices,
            new \DateTimeImmutable(),
            new \DateTimeImmutable()
        );

        $this->writer->insert($product);

        $row = $this->fetchRecord($priceId, 'product_price');

        $this->assertEquals(1000, $row->value);

        $this->writer->updateProductPrice($priceId, 1055);

        $row = $this->fetchRecord($priceId, 'product_price');

        $this->assertEquals(1055, $row->value);
        $this->assertEquals(1738497600, $row->updated_at);
    }

    public function test_updateProductPrice_throws_a_NotFoundException_if_product_price_does_not_exist()
    {
        $priceId = Uuid::uuid4();

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage("Product price {$priceId} does not exist");
        $this->writer->updateProductPrice($priceId, 100);
    }

    private function tableRowCount(string $table): int
    {
        return $this->connection->createQueryBuilder()
            ->select('*')
            ->from($table)
            ->execute()
            ->rowCount();
    }

    private function fetchRecord(UuidInterface $productId, string $table): object
    {
        return (object) $this->connection->createQueryBuilder()
            ->select('*')
            ->from($table)
            ->where('id = :id')
            ->setParameter(':id', (string) $productId)
            ->execute()
            ->fetchAssociative();
    }
}
