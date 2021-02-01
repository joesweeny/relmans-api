<?php

namespace Relmans\Boundary\Presenter;

use Relmans\Domain\Entity\Category;

class CategoryPresenter
{
    public function toObject(Category $category): object
    {
        return (object) [
            'id' => $category->getId()->toString(),
            'name' => $category->getName(),
            'createdAt' => $category->getCreatedAt()->format(DATE_RFC3339),
            'updatedAt' => $category->getUpdatedAt()->format(DATE_RFC3339),
        ];
    }
}
