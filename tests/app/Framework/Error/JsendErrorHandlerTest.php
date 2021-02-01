<?php

namespace IntelligenceFusion\Actor\Framework\Error;

use Laminas\Diactoros\ServerRequest;
use PHPUnit\Framework\TestCase;
use Slim\Exception\HttpNotFoundException;

class JsendErrorHandlerTest extends TestCase
{
    private JsendErrorHandler $handler;

    public function setUp(): void
    {
        $this->handler = new JsendErrorHandler();
    }

    public function test_invoke_returns_a_404_JsendFailResponse_if_exception_is_of_type_HttpNotFoundException()
    {
        $exception = new HttpNotFoundException(new ServerRequest());

        $response = $this->handler->__invoke(new ServerRequest(), $exception, false, false, false);

        $expectedBody = (object) [
            'status' => 'fail',
            'errors' => [
                (object) [
                    'code' => 1,
                    'message' => 'Not found',
                ],
            ],
        ];

        $this->assertEquals(json_encode($expectedBody), $response->getBody()->getContents());
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals('application/json', $response->getHeaderLine('content-type'));
    }

    public function test_invoke_returns_a_500_JsendErrorResponse_as_default_response()
    {
        $exception = new \RuntimeException();

        $response = $this->handler->__invoke(new ServerRequest(), $exception, false, false, false);

        $expectedBody = (object) [
            'status' => 'error',
            'message' => 'Internal server error',
        ];

        $this->assertEquals(json_encode($expectedBody), $response->getBody()->getContents());
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals('application/json', $response->getHeaderLine('content-type'));
    }
}
