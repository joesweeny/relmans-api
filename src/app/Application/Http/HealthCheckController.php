<?php

namespace IntelligenceFusion\Actor\Application\Http;

use IntelligenceFusion\Actor\Framework\Jsend\JsendSuccessResponse;
use Psr\Http\Message\ResponseInterface;

class HealthCheckController
{
    public function __invoke(): ResponseInterface
    {
        $data = (object) [
            'message' => 'Health Check OK',
        ];

        return new JsendSuccessResponse($data, 200);
    }
}
