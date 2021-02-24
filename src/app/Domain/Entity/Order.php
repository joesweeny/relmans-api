<?php

namespace Relmans\Domain\Entity;

use DateTimeImmutable;
use Relmans\Domain\Enum\OrderStatus;

class Order
{
    private string $id;
    private string $transactionId;
    private Customer $customer;
    /**
     * @var array|OrderItem[]
     */
    private array $items;
    private OrderStatus $status;
    private OrderMethod $method;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    public function __construct(
        string $id,
        string $transactionId,
        Customer $customer,
        OrderStatus $status,
        OrderMethod $method,
        array $items,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ) {
        $this->id = $id;
        $this->transactionId = $transactionId;
        $this->customer = $customer;
        $this->status = $status;
        $this->method = $method;
        $this->items = $items;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTransactionId(): string
    {
        return $this->transactionId;
    }

    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    public function getStatus(): OrderStatus
    {
        return $this->status;
    }

    public function getMethod(): OrderMethod
    {
        return $this->method;
    }

    /**
     * @return array|OrderItem[]
     */
    public function getItems(): array
    {
        return $this->items;
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
