<?php

namespace Relmans\Boundary\Command;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Relmans\Domain\Enum\ProductStatus;

class UpdateProductCommand
{
    private UuidInterface $productId;
    private ?ProductStatus $status;

    public function __construct(string $productId, ?string $status)
    {
        $this->productId = Uuid::fromString($productId);
        $this->status = $status !== null ? new ProductStatus($status) : $status;
    }

    public function getProductId(): UuidInterface
    {
        return $this->productId;
    }
    public function getStatus(): ?ProductStatus
    {
        return $this->status;
    }
}
