<?php

namespace Relmans\Framework\Error;

use Relmans\Framework\Jsend\JsendError;
use Relmans\Framework\Jsend\JsendErrorResponse;
use Relmans\Framework\Jsend\JsendFailResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Interfaces\ErrorHandlerInterface;
use Throwable;

class JsendErrorHandler implements ErrorHandlerInterface
{
    public function __invoke(
        ServerRequestInterface $request,
        Throwable $exception,
        bool $displayErrorDetails,
        bool $logErrors,
        bool $logErrorDetails
    ): ResponseInterface {
        if ($exception instanceof HttpNotFoundException) {
            return new JsendFailResponse([new JsendError('Not found')], 404);
        }

        return new JsendErrorResponse('Internal server error', 500);
    }
}
