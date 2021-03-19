<?php

namespace Relmans\Boundary\Command;

class GetOrderCommand
{
    private string $orderId;

    /**
     * @param string $orderId
     * @throws \InvalidArgumentException
     */
    public function __construct(string $orderId)
    {
        $this->orderId = $orderId;
    }

    public function getOrderId(): string
    {
        return $this->orderId;
    }
}
