<?php

namespace Relmans\Domain\Persistence;

use Ramsey\Uuid\UuidInterface;

class ProductReaderQuery
{
    private ?UuidInterface $categoryId;
    private ?string $term;
    private ?string $orderBy;

    public function setCategoryId(?UuidInterface $categoryId): self
    {
        $this->categoryId = $categoryId;
        return $this;
    }

    public function getCategoryId(): ?UuidInterface
    {
        return $this->categoryId ?? null;
    }

    public function setSearchTerm(?string $term): self
    {
        $this->term = $term;
        return $this;
    }

    public function getSearchTerm(): ?string
    {
        return $this->term ?? null;
    }

    public function setOrderBy(?string $orderBy): self
    {
        $this->orderBy = $orderBy;
        return $this;
    }

    public function getOrderBy(): string
    {
        return $this->orderBy ?? 'name_asc';
    }
}
