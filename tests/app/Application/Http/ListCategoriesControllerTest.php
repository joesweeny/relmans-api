<?php

namespace Relmans\Application\Http;

use League\Tactician\CommandBus;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Relmans\Boundary\Command\ListCategoriesCommand;

class ListCategoriesControllerTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var CommandBus|ObjectProphecy
     */
    private $commandBus;
    private ListCategoriesController $controller;

    public function setUp(): void
    {
        $this->commandBus = $this->prophesize(CommandBus::class);
        $this->controller = new ListCategoriesController($this->commandBus->reveal());
    }

    public function test_invoke_returns_200_response_containing_category_data()
    {
        $categories = [
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

        $this->commandBus->handle(Argument::type(ListCategoriesCommand::class))
            ->shouldBeCalled()
            ->willReturn($categories);

        $response = $this->controller->__invoke();

        $expectedBody = (object) [
            'status' => 'success',
            'data' => (object) [
                'categories' => $categories,
            ]
        ];

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($expectedBody, json_decode($response->getBody()->getContents()));
    }
}