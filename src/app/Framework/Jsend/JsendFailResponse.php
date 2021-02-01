<?php

namespace Relmans\Framework\Jsend;

use Laminas\Diactoros\Response\JsonResponse;

class JsendFailResponse extends JsonResponse
{
    /**
     * JsendFailResponse constructor.
     * @param array|JsendError[] $errors
     * @param int $status
     * @param array $headers
     */
    public function __construct(array $errors, int $status, array $headers = [])
    {
        $data = [
            'status' => 'fail',
            'errors' => array_map(static function (JsendError $error) {
                return $error->toObject();
            }, $errors),
        ];

        parent::__construct($data, $status, $headers);
    }
}
