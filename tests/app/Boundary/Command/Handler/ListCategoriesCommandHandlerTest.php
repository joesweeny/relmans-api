<?php

namespace Relmans\Boundary\Command\Handler;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Ramsey\Uuid\Uuid;
use Relmans\Boundary\Command\ListCategoriesCommand;
use Relmans\Boundary\Presenter\CategoryPresenter;
use Relmans\Domain\Entity\Category;
use Relmans\Domain\Persistence\CategoryRepository;

class ListCategoriesCommandHandlerTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var CategoryRepository|ObjectProphecy
     */
    private $repository;
    private ListCategoriesCommandHandler $handler;

    public function setUp(): void
    {
        $this->repository = $this->prophesize(CategoryRepository::class);
        $this->handler = new ListCategoriesCommandHandler($this->repository->reveal(), new CategoryPresenter());
    }

    public function test_handle_returns_an_array_of_scalar_category_objects()
    {
        $categories = [
            new Category(
                Uuid::fromString('bccd0a06-605c-43ad-bd6d-c79e6e5202f0'),
                'Fruit',
                new \DateTimeImmutable('2021-03-12T12:00:00+00:00'),
                new \DateTimeImmutable('2021-03-12T12:00:00+00:00')
            ),
            new Category(
                Uuid::fromString('6e5dcdf4-e8a7-4ef6-9cbe-c2eff8ad7eff'),
                'Vegetables',
                new \DateTimeImmutable('2021-03-12T12:00:00+00:00'),
                new \DateTimeImmutable('2021-03-12T12:00:00+00:00')
            ),
        ];

        $this->repository->get()
            ->shouldBeCalled()
            ->willReturn($categories);

        $fetched = $this->handler->handle(new ListCategoriesCommand());

        $expected = [
            (object) [
                'id' => 'bccd0a06-605c-43ad-bd6d-c79e6e5202f0',
                'name' => 'Fruit',
                'createdAt' => '2021-03-12T12:00:00+00:00',
                'updatedAt' => '2021-03-12T12:00:00+00:00',
            ],
            (object) [
                'id' => '6e5dcdf4-e8a7-4ef6-9cbe-c2eff8ad7eff',
                'name' => 'Vegetables',
                'createdAt' => '2021-03-12T12:00:00+00:00',
                'updatedAt' => '2021-03-12T12:00:00+00:00',
            ],
        ];

        $this->assertEquals($expected, $fetched);
    }
}
