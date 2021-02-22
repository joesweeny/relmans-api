<?php

namespace Relmans\Domain\Entity;

class OrderValue
{
    private int $itemTotal;
    private ?int $delivery;

    public function __construct(int $itemTotal, ?int $delivery)
    {
        $this->itemTotal = $itemTotal;
        $this->delivery = $delivery;
    }

    public function getItemTotal(): int
    {
        return $this->itemTotal;
    }

    public function getDelivery(): ?int
    {
        return $this->delivery;
    }
}
