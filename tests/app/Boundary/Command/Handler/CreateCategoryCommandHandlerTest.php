<?php

namespace Relmans\Boundary\Command\Handler;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Relmans\Boundary\Command\CreateCategoryCommand;
use Relmans\Domain\Entity\Category;
use Relmans\Domain\Persistence\CategoryRepository;
use Relmans\Framework\Time\FixedClock;

class CreateCategoryCommandHandlerTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var CategoryRepository|ObjectProphecy
     */
    private $repository;
    private CreateCategoryCommandHandler $handler;

    public function setUp(): void
    {
        $this->repository = $this->prophesize(CategoryRepository::class);
        $this->handler = new CreateCategoryCommandHandler(
            $this->repository->reveal(),
            new FixedClock(new \DateTimeImmutable('2020-03-12T12:00:00+00:00'))
        );
    }

    public function test_handle_inserts_new_Category_record_via_repository()
    {
        $command = new CreateCategoryCommand('Fruit');

        $categoryAssertion = Argument::that(function (Category $category) {
            $this->assertEquals('Fruit', $category->getName());
            $this->assertEquals(new \DateTimeImmutable('2020-03-12T12:00:00+00:00'), $category->getCreatedAt());
            $this->assertEquals(new \DateTimeImmutable('2020-03-12T12:00:00+00:00'), $category->getUpdatedAt());
            return true;
        });

        $this->repository->insert($categoryAssertion)->shouldBeCalled();

        $this->handler->handle($command);
    }
}
