<?php

namespace Relmans\Boundary\Command\Handler;

use Ramsey\Uuid\Uuid;
use Relmans\Boundary\Command\CreateCategoryCommand;
use Relmans\Domain\Entity\Category;
use Relmans\Domain\Persistence\CategoryRepository;
use Relmans\Framework\Time\Clock;

class CreateCategoryCommandHandler
{
    private CategoryRepository $repository;
    private Clock $clock;

    public function __construct(CategoryRepository $repository, Clock $clock)
    {
        $this->repository = $repository;
        $this->clock = $clock;
    }

    public function handle(CreateCategoryCommand $command): void
    {
        $category = new Category(
            Uuid::uuid4(),
            $command->getName(),
            $this->clock->now(),
            $this->clock->now(),
        );

        $this->repository->insert($category);
    }
}
