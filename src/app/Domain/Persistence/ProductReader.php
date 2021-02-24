<?php

namespace Relmans\Domain\Persistence;

use Ramsey\Uuid\UuidInterface;
use Relmans\Domain\Entity\Product;
use Relmans\Domain\Entity\ProductPrice;
use Relmans\Framework\Exception\NotFoundException;

interface ProductReader
{
    /**
     * @param ProductReaderQuery $query
     * @return array|Product[]
     */
    public function get(ProductReaderQuery $query): array;

    /**
     * @param UuidInterface $productId
     * @return Product
     * @throws NotFoundException
     */
    public function getById(UuidInterface $productId): Product;

    /**
     * @param UuidInterface $priceId
     * @return ProductPrice
     * @throws NotFoundException
     */
    public function getPriceById(UuidInterface $priceId): ProductPrice;
}
