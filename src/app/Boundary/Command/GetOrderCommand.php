<?php

namespace Relmans\Boundary\Command;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class GetOrderCommand
{
    private UuidInterface $orderId;

    /**
     * @param string $orderId
     * @throws \InvalidArgumentException
     */
    public function __construct(string $orderId)
    {
        $this->orderId = Uuid::fromString($orderId);
    }

    public function getOrderId(): UuidInterface
    {
        return $this->orderId;
    }
}
