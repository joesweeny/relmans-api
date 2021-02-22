<?php

namespace Relmans\Domain\Entity;

use Ramsey\Uuid\UuidInterface;

class OrderItem implements \JsonSerializable
{
    private UuidInterface $productId;
    private UuidInterface $priceId;
    private string $name;
    private int $size;
    private string $measurement;
    private int $price;
    private int $quantity;

    public function __construct(
        UuidInterface $productId,
        UuidInterface $priceId,
        string $name,
        int $size,
        string $measurement,
        int $price,
        int $quantity
    ) {
        $this->productId = $productId;
        $this->priceId = $priceId;
        $this->name = $name;
        $this->size = $size;
        $this->measurement = $measurement;
        $this->price = $price;
        $this->quantity = $quantity;
    }

    public function getProductId(): UuidInterface
    {
        return $this->productId;
    }

    public function getPriceId(): UuidInterface
    {
        return $this->priceId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getMeasurement(): string
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

    public function jsonSerialize()
    {
        return (object) [
            'productId' => $this->productId->toString(),
            'priceId' => $this->priceId->toString(),
            'name' => $this->name,
            'size' => $this->size,
            'measurement' => $this->measurement,
            'price' => $this->price,
            'quantity' => $this->quantity,
        ];
    }
}
