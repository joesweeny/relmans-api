<?php

namespace Relmans\Domain\Persistence;

use Relmans\Domain\Entity\Product;

interface ProductReader
{
    /**
     * @param ProductReaderQuery $query
     * @return array|Product[]
     */
    public function get(ProductReaderQuery $query): array;
}
