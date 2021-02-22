<?php

namespace Relmans\Domain\Entity;

use DateTimeImmutable;
use Ramsey\Uuid\UuidInterface;
use Relmans\Domain\Enum\OrderStatus;

class Order
{
    private UuidInterface $id;
    private string $externalId;
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
        UuidInterface $id,
        string $externalId,
        string $transactionId,
        Customer $customer,
        OrderStatus $status,
        OrderMethod $method,
        array $items,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ) {
        $this->id = $id;
        $this->externalId = $externalId;
        $this->transactionId = $transactionId;
        $this->customer = $customer;
        $this->status = $status;
        $this->method = $method;
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
