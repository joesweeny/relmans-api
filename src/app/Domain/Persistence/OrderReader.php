<?php

namespace Relmans\Domain\Persistence;

use Ramsey\Uuid\UuidInterface;
use Relmans\Domain\Entity\Order;
use Relmans\Domain\Entity\OrderItem;
use Relmans\Framework\Exception\NotFoundException;

interface OrderReader
{
    /**
     * @param string $orderId
     * @return Order
     * @throws NotFoundException
     */
    public function getById(string $orderId): Order;

    /**
     * @param OrderReaderQuery $query
     * @return array|Order[]
     */
    public function get(OrderReaderQuery $query): array;

    /**
     * @param string $orderId
     * @return array|OrderItem[]
     */
    public function getOrderItems(string $orderId): array;
}
