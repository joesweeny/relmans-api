<?php

namespace Relmans\Boundary\Command;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class ListProductsCommand
{
    private ?UuidInterface $categoryId;
    private ?string $search;

    /**
     * @param string|null $categoryId
     * @param string|null $search
     * @throws \UnexpectedValueException
     */
    public function __construct(?string $categoryId, ?string $search)
    {
        $this->categoryId = $this->validateCategoryId($categoryId);
        $this->search = $search;
    }

    public function getCategoryId(): ?UuidInterface
    {
        return $this->categoryId;
    }

    public function getSearch(): ?string
    {
        return $this->search;
    }

    /**
     * @param string|null $id
     * @return UuidInterface|null
     * @throws \UnexpectedValueException
     */
    private function validateCategoryId(?string $id): ?UuidInterface
    {
        if ($id === null) {
            return null;
        }

        return Uuid::fromString($id);
    }
}
