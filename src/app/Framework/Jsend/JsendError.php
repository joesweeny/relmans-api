<?php

namespace Relmans\Framework\Jsend;

class JsendError
{
    private string $message;
    private int $code;

    public function __construct(string $message, int $code = 1)
    {
        $this->message = $message;
        $this->code = $code;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function toObject(): object
    {
        return (object) [
            'code' => $this->getCode(),
            'message' => $this->getMessage(),
        ];
    }
}
