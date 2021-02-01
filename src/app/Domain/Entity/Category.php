<?php

namespace Relmans\Domain\Entity;

use DateTimeImmutable;
use Ramsey\Uuid\UuidInterface;

class Category
{
    private UuidInterface $id;
    private string $name;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    public function __construct(
        UuidInterface $id,
        string $name,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
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
