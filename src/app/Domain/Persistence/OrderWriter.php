<?php

namespace Relmans\Domain\Persistence;

use Ramsey\Uuid\UuidInterface;
use Relmans\Domain\Entity\Order;
use Relmans\Framework\Exception\NotFoundException;

interface OrderWriter
{
    public function insert(Order $order): void;

    /**
     * @param UuidInterface $orderId
     * @param OrderWriterQuery $query
     * @return void
     * @throws NotFoundException
     */
    public function update(UuidInterface $orderId, OrderWriterQuery $query): void;
}
