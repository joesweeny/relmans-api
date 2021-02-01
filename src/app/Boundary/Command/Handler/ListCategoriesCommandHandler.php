<?php

namespace Relmans\Boundary\Command\Handler;

use Relmans\Boundary\Command\ListCategoriesCommand;
use Relmans\Boundary\Presenter\CategoryPresenter;
use Relmans\Domain\Entity\Category;
use Relmans\Domain\Persistence\CategoryRepository;

class ListCategoriesCommandHandler
{
    /**
     * @var CategoryRepository
     */
    private CategoryRepository $repository;
    /**
     * @var CategoryPresenter
     */
    private CategoryPresenter $presenter;

    public function __construct(CategoryRepository $repository, CategoryPresenter $presenter)
    {
        $this->repository = $repository;
        $this->presenter = $presenter;
    }

    /**
     * @param ListCategoriesCommand $command
     * @return array|object[]
     */
    public function handle(ListCategoriesCommand $command): array
    {
        $categories = $this->repository->get();

        return array_map(function (Category $category) {
            return $this->presenter->toObject($category);
        }, $categories);
    }
}
