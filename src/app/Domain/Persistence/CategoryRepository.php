<?php

namespace Relmans\Domain\Persistence;

use Relmans\Domain\Entity\Category;

interface CategoryRepository
{
    public function insert(Category $category): void;

    /**
     * @return array|Category[]
     */
    public function get(): array;
}
