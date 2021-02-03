<?php

namespace Relmans\Domain\Entity;

use DateTimeImmutable;
use Ramsey\Uuid\UuidInterface;
use Relmans\Domain\Enum\ProductStatus;

class Product
{
    private UuidInterface $id;
    private UuidInterface $categoryId;
    private string $name;
    private ProductStatus $status;
    /**
     * @var array|ProductPrice[]
     */
    private array $prices;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    public function __construct(
        UuidInterface $id,
        UuidInterface $categoryId,
        string $name,
        ProductStatus $status,
        array $prices,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ) {
        $this->id = $id;
        $this->categoryId = $categoryId;
        $this->name = $name;
        $this->status = $status;
        $this->prices = $prices;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getCategoryId(): UuidInterface
    {
        return $this->categoryId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getStatus(): ProductStatus
    {
        return $this->status;
    }

    /**
     * @return array|ProductPrice[]
     */
    public function getPrices(): array
    {
        return $this->prices;
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
