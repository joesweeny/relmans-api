<?php

namespace Relmans\Domain\Persistence;

use Ramsey\Uuid\UuidInterface;
use Relmans\Domain\Entity\Order;
use Relmans\Framework\Exception\NotFoundException;

interface OrderReader
{
    /**
     * @param UuidInterface $orderId
     * @return Order
     * @throws NotFoundException
     */
    public function getById(UuidInterface $orderId): Order;
}
