<?php

namespace IntelligenceFusion\Actor\Framework\Jsend;

use PHPUnit\Framework\TestCase;

class JsendErrorResponseTest extends TestCase
{
    public function test_class_is_instantiated_as_a_new_JsendErrorResponse_object()
    {
        $response = new JsendErrorResponse('Internal server error', 500);

        $expectedBody = (object) [
            'status' => 'error',
            'message' => 'Internal server error',
        ];

        $this->assertEquals($expectedBody, json_decode($response->getBody(), false));
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertEquals('application/json', $response->getHeaderLine('content-type'));
    }
}
