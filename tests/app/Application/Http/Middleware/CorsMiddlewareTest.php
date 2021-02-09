<?php

namespace Relmans\Application\Http\Middleware;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Http\Server\RequestHandlerInterface;
use Relmans\Bootstrap\ConfigFactory;

class CorsMiddlewareTest extends TestCase
{
    use ProphecyTrait;

    private CorsMiddleware $middleware;

    public function setUp(): void
    {
        $this->middleware = new CorsMiddleware(ConfigFactory::create());
    }

    public function test_process_returns_a_response_containing_allowed_origin_header_if_request_origin_is_allowed()
    {
        $request = (new ServerRequest('POST', '/test'))->withHeader('Origin','http://localhost:3000');

        /** @var RequestHandlerInterface|ObjectProphecy $handler */
        $handler = $this->prophesize(RequestHandlerInterface::class);

        $handler->handle($request)
            ->shouldBeCalled()
            ->willReturn(new Response());

        $response = $this->middleware->process($request, $handler->reveal());

        $this->assertTrue($response->hasHeader('Access-Control-Allow-Origin'));
        $this->assertEquals('http://localhost:3000', $response->getHeaderLine('Access-Control-Allow-Origin'));
    }

    public function test_process_does_not_apply_allowed_origin_header_if_request_origin_is_not_allowed()
    {
        $request = (new ServerRequest('POST', '/test'))->withHeader('Origin','http://localhost:9000');

        /** @var RequestHandlerInterface|ObjectProphecy $handler */
        $handler = $this->prophesize(RequestHandlerInterface::class);

        $handler->handle($request)
            ->shouldBeCalled()
            ->willReturn(new Response());

        $response = $this->middleware->process($request, $handler->reveal());

        $this->assertFalse($response->hasHeader('Access-Control-Allow-Origin'));
    }
}
