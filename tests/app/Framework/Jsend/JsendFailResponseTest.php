<?php

namespace Relmans\Framework\Jsend;

use PHPUnit\Framework\TestCase;

class JsendFailResponseTest extends TestCase
{
    public function test_JsendFailResponse_formats_response_data_as_expected(): void
    {
        $errors = [
            new JsendError('111 is not a valid UUID'),
        ];

        $response = new JsendFailResponse($errors, 422);

        $expectedBody = (object) [
            'status' => 'fail',
            'errors' => [
                (object) [
                    'code' => 1,
                    'message' => '111 is not a valid UUID',
                ],
            ],
        ];

        $this->assertEquals($expectedBody, json_decode($response->getBody(), false));
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertEquals('application/json', $response->getHeaderLine('content-type'));
    }

    public function test_JsendFailResponse_data_parameter_can_handle_validation_error_arrays(): void
    {
        $data = [
            new JsendError('111 is not a valid UUID'),
            new JsendError( 'Name field exceeds the limit', 2),
        ];

        $response = new JsendFailResponse($data, 422);

        $expectedBody = (object) [
            'status' => 'fail',
            'errors' => [
                (object) [
                    'code' => 1,
                    'message' => '111 is not a valid UUID',
                ],
                (object) [
                    'code' => 2,
                    'message' => 'Name field exceeds the limit',
                ]
            ],
        ];

        $this->assertEquals($expectedBody, json_decode($response->getBody(), false));
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertEquals('application/json', $response->getHeaderLine('content-type'));
    }
}
