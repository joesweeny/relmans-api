<?php

namespace Relmans\Boundary\Command;

class DeleteOrderCommand
{
    private string $orderId;

    public function __construct(string $orderId)
    {
        $this->orderId = $orderId;
    }

    public function getOrderId(): string
    {
        return $this->orderId;
    }
}
