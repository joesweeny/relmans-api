<?php

namespace Relmans\Boundary\Command;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class ListProductsCommand
{
    private ?UuidInterface $categoryId;
    private ?string $search;
    private ?string $orderBy;

    /**
     * @param string|null $categoryId
     * @param string|null $search
     * @param string|null $orderBy
     * @throws \InvalidArgumentException
     */
    public function __construct(?string $categoryId, ?string $search, ?string $orderBy)
    {
        $this->categoryId = $this->validateCategoryId($categoryId);
        $this->search = $search;
        $this->orderBy = $orderBy;
    }

    public function getCategoryId(): ?UuidInterface
    {
        return $this->categoryId;
    }

    public function getSearch(): ?string
    {
        return $this->search;
    }

    public function getOrderBy(): ?string
    {
        return $this->orderBy;
    }

    /**
     * @param string|null $id
     * @return UuidInterface|null
     * @throws \InvalidArgumentException
     */
    private function validateCategoryId(?string $id): ?UuidInterface
    {
        if ($id === null) {
            return null;
        }

        return Uuid::fromString($id);
    }
}
