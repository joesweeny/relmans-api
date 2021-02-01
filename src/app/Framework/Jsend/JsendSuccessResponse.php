<?php

namespace IntelligenceFusion\Actor\Framework\Jsend;

use Laminas\Diactoros\Response\JsonResponse;

class JsendSuccessResponse extends JsonResponse
{
    public function __construct(?object $data, int $status, array $headers = [])
    {
        $body = (object) [
            'status' => 'success',
            'data' => $data,
        ];

        parent::__construct($body, $status, $headers);
    }
}
