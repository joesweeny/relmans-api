<?php

namespace Relmans\Domain\Persistence;

use Relmans\Domain\Entity\Product;

interface ProductReader
{
    /**
     * @param ProductRepositoryQuery $query
     * @return array|Product[]
     */
    public function get(ProductRepositoryQuery $query): array;
}
