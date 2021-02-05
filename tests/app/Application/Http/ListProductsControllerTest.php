<?php

namespace Relmans\Application\Http;

use GuzzleHttp\Psr7\ServerRequest;
use League\Tactician\CommandBus;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Ramsey\Uuid\Uuid;
use Relmans\Boundary\Command\ListProductsCommand;

class ListProductsControllerTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var CommandBus|ObjectProphecy
     */
    private $commandBus;
    private ListProductsController $controller;

    public function setUp(): void
    {
        $this->commandBus = $this->prophesize(CommandBus::class);
        $this->controller = new ListProductsController($this->commandBus->reveal());
    }

    public function test_invoke_returns_a_200_response_containing_product_information()
    {
        $params = [
            'categoryId' => '0704e476-327a-4a2b-8652-6d102d5e1ca9',
            'search' => 'veg',
            'order' => 'name_asc',
        ];

        $request = (new ServerRequest('GET', '/product'))->withQueryParams($params);

        $commandAssertion = Argument::that(function (ListProductsCommand $command) {
            $this->assertEquals(Uuid::fromString('0704e476-327a-4a2b-8652-6d102d5e1ca9'), $command->getCategoryId());
            $this->assertEquals('veg', $command->getSearch());
            $this->assertEquals('name_asc', $command->getOrderBy());
            return true;
        });

        $products = [
            (object) [
                'id' => '891e050b-e794-4000-a5fd-0960aac75034',
                'name' => 'Golden Delicious Apples',
                'categoryId' => 'c4dd8587-66c5-47f1-8fb5-914aaef60a5b',
                'status' => 'IN_STOCK',
                'prices' => [
                    (object) [
                        'id' => '21c383d5-fa7c-44fb-a322-7ae581bbc895',
                        'value' => 1000,
                        'size' => 1.5,
                        'measurement' => 'KILOGRAMS',
                    ],
                    (object) [
                        'id' => '6d6cd6d9-12f1-4237-88f7-5653ecc93df9',
                        'value' => 1000,
                        'size' => 500.0,
                        'measurement' => 'GRAMS',
                    ],
                ]
            ],
        ];

        $this->commandBus->handle($commandAssertion)
            ->shouldBeCalled()
            ->willReturn($products);

        $response = $this->controller->__invoke($request);

        $expected = (object) [
            'status' => 'success',
            'data' => (object) [
                'products' => $products,
            ]
        ];

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($expected, json_decode($response->getBody()->getContents()));
    }

    public function test_invoke_returns_a_422_response_if_categoryId_query_parameter_is_not_the_correct_schema()
    {
        $params = [
            'categoryId' => '0',
            'search' => 'veg',
            'order' => 'name_asc',
        ];

        $request = (new ServerRequest('GET', '/product'))->withQueryParams($params);

        $this->commandBus->handle(Argument::type(ListProductsCommand::class))->shouldNotBeCalled();

        $response = $this->controller->__invoke($request);

        $expected = (object) [
            'status' => 'fail',
            'errors' => [
                (object) [
                    'code' => 1,
                    'message' => 'Invalid UUID string: 0',
                ]
            ]
        ];

        $this->assertEquals(422, $response->getStatusCode());
        $this->assertEquals($expected, json_decode($response->getBody()->getContents()));
    }

    public function test_invoke_returns_a_200_response_when_no_query_parameters_are_provided()
    {
        $request = new ServerRequest('GET', '/product');

        $commandAssertion = Argument::that(function (ListProductsCommand $command) {
            $this->assertNull($command->getCategoryId());
            $this->assertNull($command->getSearch());
            $this->assertNull($command->getOrderBy());
            return true;
        });

        $products = [
            (object) [
                'id' => '891e050b-e794-4000-a5fd-0960aac75034',
                'name' => 'Golden Delicious Apples',
                'categoryId' => 'c4dd8587-66c5-47f1-8fb5-914aaef60a5b',
                'status' => 'IN_STOCK',
                'prices' => [
                    (object) [
                        'id' => '21c383d5-fa7c-44fb-a322-7ae581bbc895',
                        'value' => 1000,
                        'size' => 1.5,
                        'measurement' => 'KILOGRAMS',
                    ],
                    (object) [
                        'id' => '6d6cd6d9-12f1-4237-88f7-5653ecc93df9',
                        'value' => 1000,
                        'size' => 500.0,
                        'measurement' => 'GRAMS',
                    ],
                ]
            ],
        ];

        $this->commandBus->handle($commandAssertion)
            ->shouldBeCalled()
            ->willReturn($products);

        $response = $this->controller->__invoke($request);

        $expected = (object) [
            'status' => 'success',
            'data' => (object) [
                'products' => $products,
            ]
        ];

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($expected, json_decode($response->getBody()->getContents()));
    }
}
