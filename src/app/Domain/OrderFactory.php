<?php

namespace Relmans\Domain;

use Relmans\Domain\Entity\Customer;
use Relmans\Domain\Entity\Order;
use Relmans\Domain\Entity\OrderMethod;

class OrderFactory
{
    public function createNewOrder(string $orderNumber, Customer $customer, OrderMethod $method, array $items): Order
    {
        
    }
}
