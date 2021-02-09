<?php

namespace Relmans\Domain\Persistence;

use Ramsey\Uuid\UuidInterface;
use Relmans\Domain\Entity\Product;
use Relmans\Framework\Exception\NotFoundException;

interface ProductWriter
{
    public function insert(Product $product): void;

    /**
     * @param UuidInterface $id
     * @param ProductWriterQuery $query
     * @return void
     * @throws NotFoundException
     */
    public function updateProduct(UuidInterface $id, ProductWriterQuery $query): void;

    /**
     * @param UuidInterface $priceId
     * @param int $value
     * @return void
     * @throws NotFoundException
     */
    public function updateProductPrice(UuidInterface $priceId, int $value): void;
}
