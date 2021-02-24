<?php

namespace Relmans\Boundary\Utility;

use Ramsey\Uuid\UuidInterface;

class OrderItemData
{
    private UuidInterface $productId;
    private UuidInterface $priceId;
    private int $price;
    private int $quantity;

    public function __construct(UuidInterface $productId, UuidInterface $priceId, int $price, int $quantity)
    {
        $this->productId = $productId;
        $this->priceId = $priceId;
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

    public function getPrice(): int
    {
        return $this->price;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }
}
