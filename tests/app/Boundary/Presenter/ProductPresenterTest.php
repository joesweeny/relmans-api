<?php

namespace Relmans\Boundary\Presenter;

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Relmans\Domain\Entity\Product;
use Relmans\Domain\Entity\ProductPrice;
use Relmans\Domain\Enum\Measurement;
use Relmans\Domain\Enum\ProductStatus;

class ProductPresenterTest extends TestCase
{
    public function test_toObject_returns_a_scalar_object_containing_Product_properties()
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

        $product = new Product(
            $productId,
            Uuid::fromString('c4dd8587-66c5-47f1-8fb5-914aaef60a5b'),
            'Golden Delicious Apples',
            ProductStatus::IN_STOCK(),
            false,
            $prices,
            new \DateTimeImmutable(),
            new \DateTimeImmutable()
        );

        $scalar = (new ProductPresenter())->toObject($product);

        $expected = (object) [
            'id' => '891e050b-e794-4000-a5fd-0960aac75034',
            'name' => 'Golden Delicious Apples',
            'categoryId' => 'c4dd8587-66c5-47f1-8fb5-914aaef60a5b',
            'status' => 'IN_STOCK',
            'featured' => false,
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
        ];

        $this->assertEquals($expected, $scalar);
    }
}
