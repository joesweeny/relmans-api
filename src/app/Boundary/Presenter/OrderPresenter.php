<?php

namespace Relmans\Boundary\Presenter;

use Relmans\Domain\Entity\Order;
use Relmans\Domain\Entity\OrderItem;

class OrderPresenter
{
    public function toObject(Order $order): object
    {
        return (object) [
            'id' => $order->getId()->toString(),
            'externalId' => $order->getExternalId(),
            'transactionId' => $order->getTransactionId(),
            'customer' => $order->getCustomer()->jsonSerialize(),
            'status' => $order->getStatus()->getValue(),
            'method' => (object) [
                'type' => $order->getMethod()->getFulfilmentType()->getValue(),
                'date' => $order->getMethod()->getDate()->format(DATE_RFC3339),
                'fee' => $order->getMethod()->getFee(),
            ],
            'items' => array_map(function (OrderItem $item) {
                return $this->orderItemToObject($item);
            }, $order->getItems()),
            'createdAt' => $order->getCreatedAt()->format(DATE_RFC3339),
            'updatedAt' => $order->getUpdatedAt()->format(DATE_RFC3339),
        ];
    }

    private function orderItemToObject(OrderItem $item): object
    {
        return (object) [
            'id' => $item->getId()->toString(),
            'orderId' => $item->getOrderId()->toString(),
            'productId' => $item->getProductId()->toString(),
            'name' => $item->getName(),
            'price' => $item->getPrice(),
            'size' => $item->getSize(),
            'measurement' => $item->getMeasurement()->getValue(),
            'quantity' => $item->getQuantity(),
            'createdAt' => $item->getCreatedAt()->format(DATE_RFC3339),
            'updatedAt' => $item->getUpdatedAt()->format(DATE_RFC3339),
        ];
    }
}
