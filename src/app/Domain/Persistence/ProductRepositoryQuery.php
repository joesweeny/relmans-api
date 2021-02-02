<?php

namespace Relmans\Domain\Persistence;

use Ramsey\Uuid\UuidInterface;

class ProductRepositoryQuery
{
    private ?UuidInterface $categoryId;
    private ?string $term;

    public function setCategoryId(UuidInterface $categoryId): self
    {
        $this->categoryId = $categoryId;
        return $this;
    }

    public function getCategoryId(): ?UuidInterface
    {
        return $this->categoryId;
    }

    public function setSearchTerm(string $term): self
    {
        $this->term = $term;
        return $this;
    }

    public function getSearchTerm(): ?string
    {
        return $this->term;
    }
}
