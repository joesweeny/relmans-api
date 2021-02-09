<?php

namespace Relmans\Application\Http;

use GuzzleHttp\Psr7\ServerRequest;
use League\Tactician\CommandBus;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Http\Message\ServerRequestInterface;
use Ramsey\Uuid\Uuid;
use Relmans\Boundary\Command\UpdateProductCommand;
use Relmans\Domain\Enum\ProductStatus;
use Relmans\Framework\Exception\NotFoundException;

class UpdateProductControllerTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var CommandBus|ObjectProphecy
     */
    private $commandBus;
    private UpdateProductController $controller;

    public function setUp(): void
    {
        $this->commandBus = $this->prophesize(CommandBus::class);
        $this->controller = new UpdateProductController($this->commandBus->reveal());
    }

    public function test_invoke_returns_a_204_response()
    {
        $body = (object) [
            'status' => 'IN_STOCK',
            'featured' => true,
        ];

        $request = $this->request(json_encode($body));

        $commandAssertion = Argument::that(function (UpdateProductCommand $command) {
            $this->assertEquals(Uuid::fromString('3e478e96-8851-4474-8def-4a5027a7d272'), $command->getProductId());
            $this->assertEquals(ProductStatus::IN_STOCK(), $command->getStatus());
            $this->assertTrue($command->getFeatured());
            return true;
        });

        $this->commandBus->handle($commandAssertion)->shouldBeCalled();

        $response = $this->controller->__invoke($request);

        $this->assertEquals(204, $response->getStatusCode());
        $this->assertEmpty($response->getBody()->getContents());
    }

    public function test_invoke_returns_a_400_response_if_unable_to_parse_request_body()
    {
        $request = $this->request('{"invalidJson": "yes"');

        $this->commandBus->handle(Argument::type(UpdateProductCommand::class))->shouldNotBeCalled();

        $response = $this->controller->__invoke($request);

        $expectedBody = (object) [
            'status' => 'fail',
            'errors' => [
                (object) [
                    'code' => 1,
                    'message' => 'Syntax error',
                ]
            ],
        ];

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals($expectedBody, json_decode($response->getBody()->getContents()));
    }

    public function test_invoke_returns_a_400_response_if_request_body_is_empty()
    {
        $request = $this->request(null);

        $this->commandBus->handle(Argument::type(UpdateProductCommand::class))->shouldNotBeCalled();

        $response = $this->controller->__invoke($request);

        $expectedBody = (object) [
            'status' => 'fail',
            'errors' => [
                (object) [
                    'code' => 1,
                    'message' => 'Syntax error',
                ]
            ],
        ];

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals($expectedBody, json_decode($response->getBody()->getContents()));
    }

    public function test_invoke_returns_a_422_response_if_request_body_does_not_contain_the_expected_schema()
    {
        $body = (object) [
            'status' => 'INVALID',
        ];

        $request = $this->request(json_encode($body));

        $this->commandBus->handle(Argument::type(UpdateProductCommand::class))->shouldNotBeCalled();

        $response = $this->controller->__invoke($request);

        $expectedBody = (object) [
            'status' => 'fail',
            'errors' => [
                (object) [
                    'code' => 1,
                    'message' => "Value 'INVALID' is not part of the enum Relmans\Domain\Enum\ProductStatus",
                ]
            ],
        ];

        $this->assertEquals(422, $response->getStatusCode());
        $this->assertEquals($expectedBody, json_decode($response->getBody()->getContents()));
    }

    public function test_invoke_returns_a_404_response_if_id_path_parameter_is_not_a_valid_uuid_string()
    {
        $body = (object) [
            'status' => 'OUT_OF_STOCK',
        ];

        $request = new ServerRequest('POST', '/product', [], json_encode($body));
        $request = $request->withAttribute('id', '3');

        $this->commandBus->handle(Argument::type(UpdateProductCommand::class))->shouldNotBeCalled();

        $response = $this->controller->__invoke($request);

        $expectedBody = (object) [
            'status' => 'fail',
            'errors' => [
                (object) [
                    'code' => 1,
                    'message' => 'Invalid UUID string: 3',
                ]
            ],
        ];

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals($expectedBody, json_decode($response->getBody()->getContents()));
    }

    public function test_invoke_returns_a_404_response_if_product_resource_does_not_exist()
    {
        $body = (object) [
            'status' => 'IN_STOCK',
        ];

        $request = $this->request(json_encode($body));

        $commandAssertion = Argument::that(function (UpdateProductCommand $command) {
            $this->assertEquals(Uuid::fromString('3e478e96-8851-4474-8def-4a5027a7d272'), $command->getProductId());
            $this->assertEquals(ProductStatus::IN_STOCK(), $command->getStatus());
            $this->assertNull($command->getFeatured());
            return true;
        });

        $this->commandBus->handle($commandAssertion)
            ->shouldBeCalled()
            ->willThrow(new NotFoundException('Product does not exist'));

        $response = $this->controller->__invoke($request);

        $expectedBody = (object) [
            'status' => 'fail',
            'errors' => [
                (object) [
                    'code' => 1,
                    'message' => 'Product does not exist',
                ]
            ],
        ];

        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals($expectedBody, json_decode($response->getBody()->getContents()));
    }

    private function request(?string $body): ServerRequestInterface
    {
        $request = new ServerRequest('PATCH', '', [], $body);
        return $request->withAttribute('id', '3e478e96-8851-4474-8def-4a5027a7d272');
    }
}
