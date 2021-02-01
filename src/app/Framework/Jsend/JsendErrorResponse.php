<?php

namespace Relmans\Framework\Jsend;

use Laminas\Diactoros\Response\JsonResponse;

class JsendErrorResponse extends JsonResponse
{
    public function __construct(string $message, int $status, array $headers = [])
    {
        $data = [
            'status' => 'error',
            'message' => $message,
        ];

        parent::__construct($data, $status, $headers);
    }
}
