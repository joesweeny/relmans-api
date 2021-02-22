<?php

namespace Relmans\Domain\Entity;

use DateTimeImmutable;
use Ramsey\Uuid\UuidInterface;
use Relmans\Domain\Enum\OrderStatus;

class Order
{
    private UuidInterface $id;
    private string $externalId;
    private Customer $customer;
    /**
     * @var array|OrderItem[]
     */
    private array $items;
    private OrderStatus $status;
    private OrderValue $value;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    public function __construct(
        UuidInterface $id,
        string $externalId,
        Customer $customer,
        OrderStatus $status,
        OrderValue $value,
        array $items,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ) {
        $this->id = $id;
        $this->externalId = $externalId;
        $this->customer = $customer;
        $this->status = $status;
        $this->value = $value;
        $this->items = $items;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getExternalId(): string
    {
        return $this->externalId;
    }

    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    public function getStatus(): OrderStatus
    {
        return $this->status;
    }

    /**
     * @return array|OrderItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function getValue(): OrderValue
    {
        return $this->value;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
