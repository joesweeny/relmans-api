<?php

namespace Relmans\Domain\Persistence;

use Relmans\Domain\Entity\Order;

interface OrderWriter
{
    public function insert(Order $order): void;
}
