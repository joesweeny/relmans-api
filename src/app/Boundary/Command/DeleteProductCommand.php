<?php

namespace Relmans\Boundary\Command;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class DeleteProductCommand
{
    private UuidInterface $productId;

    /**
     * @param string $productId
     * @throws \InvalidArgumentException
     */
    public function __construct(string $productId)
    {
        $this->productId = Uuid::fromString($productId);
    }

    public function getProductId(): UuidInterface
    {
        return $this->productId;
    }
}
