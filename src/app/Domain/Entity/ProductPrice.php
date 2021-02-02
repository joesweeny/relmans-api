<?php

namespace Relmans\Domain\Entity;

use Ramsey\Uuid\UuidInterface;
use Relmans\Domain\Enum\Measurement;

class ProductPrice
{
    private UuidInterface $id;
    private UuidInterface $productId;
    private int $price;
    private float $size;
    private Measurement $measurement;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    public function __construct(
        UuidInterface $id,
        UuidInterface $productId,
        int $price,
        float $size,
        Measurement $measurement,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ) {
        $this->id = $id;
        $this->productId = $productId;
        $this->price = $price;
        $this->size = $size;
        $this->measurement = $measurement;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getProductId(): UuidInterface
    {
        return $this->productId;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function getSize(): float
    {
        return $this->size;
    }

    public function getMeasurement(): Measurement
    {
        return $this->measurement;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
