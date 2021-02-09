<?php

namespace Relmans\Boundary\Command;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Relmans\Domain\Enum\ProductStatus;

class UpdateProductCommand
{
    private UuidInterface $productId;
    private ?ProductStatus $status;
    private ?bool $featured;

    /**
     * UpdateProductCommand constructor.
     * @param string $productId
     * @param string|null $status
     * @param ?bool $featured
     * @throws \InvalidArgumentException
     * @throws \UnexpectedValueException
     */
    public function __construct(string $productId, ?string $status, ?bool $featured)
    {
        $this->productId = Uuid::fromString($productId);
        $this->status = $status !== null ? new ProductStatus($status) : $status;
        $this->featured = $featured;
    }

    public function getProductId(): UuidInterface
    {
        return $this->productId;
    }
    public function getStatus(): ?ProductStatus
    {
        return $this->status;
    }

    public function getFeatured(): ?bool
    {
        return $this->featured;
    }
}
