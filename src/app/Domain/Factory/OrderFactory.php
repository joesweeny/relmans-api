<?php

namespace Relmans\Domain\Factory;

use Ramsey\Uuid\Uuid;
use Relmans\Boundary\Utility\OrderItemData;
use Relmans\Domain\Entity\Customer;
use Relmans\Domain\Entity\Order;
use Relmans\Domain\Entity\OrderItem;
use Relmans\Domain\Entity\OrderMethod;
use Relmans\Domain\Enum\OrderStatus;
use Relmans\Domain\Persistence\ProductReader;
use Relmans\Framework\Exception\NotFoundException;
use Relmans\Framework\Exception\ValidationException;
use Relmans\Framework\Time\Clock;

class OrderFactory
{
    private ProductReader $productReader;
    private Clock $clock;

    public function __construct(ProductReader $productReader, Clock $clock)
    {
        $this->productReader = $productReader;
        $this->clock = $clock;
    }

    /**
     * @param string $orderNumber
     * @param Customer $customer
     * @param OrderMethod $method
     * @param array $items
     * @return Order
     * @throws ValidationException
     */
    public function createNewOrder(string $orderNumber, Customer $customer, OrderMethod $method, array $items): Order
    {
        return new Order(
            $orderNumber,
            '',
            $customer,
            OrderStatus::PENDING(),
            $method,
            $this->parseOrderItems($items, $orderNumber),
            $this->clock->now(),
            $this->clock->now()
        );
    }

    /**
     * @param array $items
     * @param string $orderNumber
     * @return array|OrderItem[]
     * @throws ValidationException
     */
    private function parseOrderItems(array $items, string $orderNumber): array
    {
        return array_map(function (OrderItemData $item) use ($orderNumber) {
            try {
                $product = $this->productReader->getById($item->getProductId());
            } catch (NotFoundException $e) {
                throw new ValidationException($e->getMessage());
            }

            try {
                $price = $this->productReader->getPriceById($item->getPriceId());
            } catch (NotFoundException $e) {
                throw new ValidationException($e->getMessage());
            }

            if (!$price->getProductId()->equals($product->getId())) {
                throw new ValidationException("Price {$item->getPriceId()} is not associated to product {$item->getProductId()}");
            }

            if ($price->getValue() !== $item->getPrice()) {
                throw new ValidationException("Incorrect price provided for price item {$item->getPriceId()}");
            }

            return new OrderItem(
                Uuid::uuid4(),
                $orderNumber,
                $product->getId(),
                $product->getName(),
                $price->getValue(),
                $price->getSize(),
                $price->getMeasurement(),
                $item->getQuantity(),
                $this->clock->now(),
                $this->clock->now()
            );
        }, $items);
    }
}
