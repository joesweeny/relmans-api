<?php

namespace Relmans\Domain\Persistence\Doctrine;

use Ramsey\Uuid\Uuid;
use Relmans\Domain\Entity\Address;
use Relmans\Domain\Entity\Customer;
use Relmans\Domain\Entity\Order;
use Relmans\Domain\Entity\OrderItem;
use Relmans\Domain\Entity\OrderMethod;
use Relmans\Domain\Enum\FulfilmentType;
use Relmans\Domain\Enum\Measurement;
use Relmans\Domain\Enum\OrderStatus;

class OrderHydrator
{
    /**
     * @param object $row
     * @param array|array[] $items
     * @return Order
     */
    public function hydrateOrder(object $row, array $items): Order
    {
        $items = array_map(function (array $item) {
            return $this->hydrateOrderItem((object) $item);
        }, $items);

        return new Order(
            Uuid::fromString($row->id),
            $row->external_id,
            $row->transaction_id,
            $this->hydrateCustomer($row->customer_details),
            new OrderStatus($row->status),
            $this->hydrateOrderMethod($row->method),
            $items,
            \DateTimeImmutable::createFromFormat('U', $row->created_at),
            \DateTimeImmutable::createFromFormat('U', $row->updated_at)
        );
    }

    public function hydrateOrderItem(object $row): OrderItem
    {
        return new OrderItem(
            Uuid::fromString($row->id),
            Uuid::fromString($row->order_id),
            Uuid::fromString($row->product_id),
            $row->name,
            $row->price,
            $row->size,
            new Measurement($row->measurement),
            $row->quantity,
            \DateTimeImmutable::createFromFormat('U', $row->created_at),
            \DateTimeImmutable::createFromFormat('U', $row->updated_at)
        );
    }

    private function hydrateCustomer(string $customer): Customer
    {
        $customer = json_decode($customer);
        $address = $customer->address;

        return new Customer(
            $customer->firstName,
            $customer->lastName,
            new Address(
                $address->line1,
                $address->line2,
                $address->line3,
                $address->town,
                $address->county,
                $address->postCode
            ),
            $customer->phoneNumber
        );
    }

    private function hydrateOrderMethod(string $method): OrderMethod
    {
        $method = json_decode($method);

        return new OrderMethod(
            new FulfilmentType($method->type),
            \DateTimeImmutable::createFromFormat('U', $method->date),
            $method->fee
        );
    }
}
