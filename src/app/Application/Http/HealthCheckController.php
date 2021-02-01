<?php

namespace Relmans\Application\Http;

use Relmans\Framework\Jsend\JsendSuccessResponse;
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
