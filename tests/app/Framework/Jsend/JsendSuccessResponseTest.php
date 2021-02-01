<?php

namespace Relmans\Framework\Jsend;

use PHPUnit\Framework\TestCase;

class JsendSuccessResponseTest extends TestCase
{
    public function test_JsendSuccessResponse_formats_response_data_as_expected(): void
    {
        $data = (object) [
            'id' => '5e3e62b9-774f-44cd-84d5-a539c8dfbe12',
        ];

        $response = new JsendSuccessResponse($data, 201);

        $expectedBody = (object) [
            'status' => 'success',
            'data' => (object) [
                'id' => '5e3e62b9-774f-44cd-84d5-a539c8dfbe12',
            ],
        ];

        $this->assertEquals($expectedBody, json_decode($response->getBody(), false));
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals('application/json', $response->getHeaderLine('content-type'));
    }

    public function test_JsendSuccessResponse_data_constructor_argument_can_be_null()
    {
        $response = new JsendSuccessResponse(null, 200);

        $expectedBody = (object) [
            'status' => 'success',
            'data' => null,
        ];

        $this->assertEquals($expectedBody, json_decode($response->getBody(), false));
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->getHeaderLine('content-type'));
    }
}
