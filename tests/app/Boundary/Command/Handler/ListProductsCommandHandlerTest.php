<?php

namespace app\Boundary\Command\Handler;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Ramsey\Uuid\Uuid;
use Relmans\Boundary\Command\Handler\ListProductsCommandHandler;
use Relmans\Boundary\Command\ListProductsCommand;
use Relmans\Boundary\Presenter\ProductPresenter;
use Relmans\Domain\Entity\Product;
use Relmans\Domain\Entity\ProductPrice;
use Relmans\Domain\Enum\Measurement;
use Relmans\Domain\Enum\ProductStatus;
use Relmans\Domain\Persistence\ProductReader;
use Relmans\Domain\Persistence\ProductReaderQuery;

class ListProductsCommandHandlerTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var ProductReader|ObjectProphecy
     */
    private $reader;
    private ListProductsCommandHandler $handler;

    public function setUp(): void
    {
        $this->reader = $this->prophesize(ProductReader::class);
        $this->handler = new ListProductsCommandHandler(
            $this->reader->reveal(),
            new ProductPresenter()
        );
    }

    public function test_handle_returns_an_array_of_scalar_product_objects()
    {
        $command = new ListProductsCommand('1b9898a6-7cab-400d-8fd0-aa7903af617a', 'veg','name_asc');

        $queryAssertion = Argument::that(function (ProductReaderQuery $query) {
            $this->assertEquals(Uuid::fromString('1b9898a6-7cab-400d-8fd0-aa7903af617a'), $query->getCategoryId());
            $this->assertEquals('veg', $query->getSearchTerm());
            $this->assertEquals('name_asc', $query->getOrderBy());
            return true;
        });

        $products = [
            $this->product(),
        ];

        $this->reader->get($queryAssertion)
            ->shouldBeCalled()
            ->willReturn($products);

        $fetched = $this->handler->handle($command);

        $expected = [
            (object) [
                'id' => '891e050b-e794-4000-a5fd-0960aac75034',
                'name' => 'Golden Delicious Apples',
                'categoryId' => 'c4dd8587-66c5-47f1-8fb5-914aaef60a5b',
                'status' => 'IN_STOCK',
                'prices' => [
                    (object) [
                        'id' => '21c383d5-fa7c-44fb-a322-7ae581bbc895',
                        'value' => 1000,
                        'size' => 1.5,
                        'measurement' => 'KILOGRAMS',
                    ],
                    (object) [
                        'id' => '6d6cd6d9-12f1-4237-88f7-5653ecc93df9',
                        'value' => 1000,
                        'size' => 500.0,
                        'measurement' => 'GRAMS',
                    ],
                ]
            ],
        ];

        $this->assertEquals($expected, $fetched);
    }

    private function product(): Product
    {
        $productId = Uuid::fromString('891e050b-e794-4000-a5fd-0960aac75034');

        $prices = [
            new ProductPrice(
                Uuid::fromString('21c383d5-fa7c-44fb-a322-7ae581bbc895'),
                $productId,
                1000,
                1.5,
                Measurement::KILOGRAMS(),
                new \DateTimeImmutable(),
                new \DateTimeImmutable()
            ),
            new ProductPrice(
                Uuid::fromString('6d6cd6d9-12f1-4237-88f7-5653ecc93df9'),
                $productId,
                1000,
                500,
                Measurement::GRAMS(),
                new \DateTimeImmutable(),
                new \DateTimeImmutable()
            )
        ];

        return new Product(
            $productId,
            Uuid::fromString('c4dd8587-66c5-47f1-8fb5-914aaef60a5b'),
            'Golden Delicious Apples',
            ProductStatus::IN_STOCK(),
            $prices,
            new \DateTimeImmutable(),
            new \DateTimeImmutable()
        );
    }
}
