<?php

namespace Relmans\Domain\Entity;

use DateTimeImmutable;
use Ramsey\Uuid\UuidInterface;
use Relmans\Domain\Enum\Measurement;

class OrderItem
{
    private UuidInterface $id;
    private string $orderId;
    private UuidInterface $productId;
    private string $name;
    private int $price;
    private int $size;
    private Measurement $measurement;
    private int $quantity;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    public function __construct(
        UuidInterface $id,
        string $orderId,
        UuidInterface $productId,
        string $name,
        int $price,
        int $size,
        Measurement $measurement,
        int $quantity,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ) {
        $this->id = $id;
        $this->orderId = $orderId;
        $this->productId = $productId;
        $this->name = $name;
        $this->price = $price;
        $this->size = $size;
        $this->measurement = $measurement;
        $this->quantity = $quantity;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getOrderId(): string
    {
        return $this->orderId;
    }

    public function getProductId(): UuidInterface
    {
        return $this->productId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getMeasurement(): Measurement
    {
        return $this->measurement;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
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
