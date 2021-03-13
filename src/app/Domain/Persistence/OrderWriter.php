<?php

namespace Relmans\Domain\Persistence;

use Ramsey\Uuid\UuidInterface;
use Relmans\Domain\Entity\Order;
use Relmans\Framework\Exception\NotFoundException;

interface OrderWriter
{
    public function insert(Order $order): void;

    /**
     * @param string $orderId
     * @param OrderWriterQuery $query
     * @return void
     * @throws NotFoundException
     */
    public function update(string $orderId, OrderWriterQuery $query): void;

    public function delete(string $orderId): void;
}
