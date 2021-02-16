<?php

namespace Relmans\Boundary\Command\Handler;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
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

    public function test_handle_inserts_new_Category_record_via_repository_and_returns_category_id()
    {
        /** @var CreateCategoryCommand|ObjectProphecy $command */
        $command = $this->prophesize(CreateCategoryCommand::class);
        $command->getName()->willReturn('Fruit');
        $command->getId()->willReturn(Uuid::fromString('40242cb7-ca07-4159-b180-8f8ccfb555c5'));

        $categoryAssertion = Argument::that(function (Category $category) {
            $this->assertEquals(Uuid::fromString('40242cb7-ca07-4159-b180-8f8ccfb555c5'), $category->getId());
            $this->assertEquals('Fruit', $category->getName());
            $this->assertEquals(new \DateTimeImmutable('2020-03-12T12:00:00+00:00'), $category->getCreatedAt());
            $this->assertEquals(new \DateTimeImmutable('2020-03-12T12:00:00+00:00'), $category->getUpdatedAt());
            return true;
        });

        $this->repository->insert($categoryAssertion)->shouldBeCalled();

        $id = $this->handler->handle($command->reveal());

        $this->assertEquals('40242cb7-ca07-4159-b180-8f8ccfb555c5', $id);
    }
}
