<?php

namespace Relmans\Domain\Persistence;

use Ramsey\Uuid\UuidInterface;
use Relmans\Domain\Entity\Product;
use Relmans\Domain\Enum\ProductStatus;
use Relmans\Framework\Exception\NotFoundException;

interface ProductWriter
{
    public function insert(Product $product): void;

    /**
     * @param UuidInterface $id
     * @param ProductStatus $status
     * @return void
     * @throws NotFoundException
     */
    public function updateProductStatus(UuidInterface $id, ProductStatus $status): void;

    /**
     * @param UuidInterface $priceId
     * @param int $value
     * @return void
     * @throws NotFoundException
     */
    public function updateProductPrice(UuidInterface $priceId, int $value): void;
}
