<?php

namespace Relmans\Application\Http;

use League\Tactician\CommandBus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Relmans\Boundary\Command\CreateOrderCommand;
use Relmans\Framework\Exception\ValidationException;
use Relmans\Framework\Jsend\JsendError;
use Relmans\Framework\Jsend\JsendFailResponse;
use Relmans\Framework\Jsend\JsendSuccessResponse;

class CreateOrderController
{
    private CommandBus $bus;

    public function __construct(CommandBus $bus)
    {
        $this->bus = $bus;
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $body = json_decode(
                $request->getBody()->getContents(),
                false,
                512,
                JSON_THROW_ON_ERROR
            );
        } catch (\JsonException $e) {
            return new JsendFailResponse([new JsendError($e->getMessage())], 400);
        }

        if (!$body) {
            return new JsendFailResponse([new JsendError('Unable to parse request body')], 400);
        }

        try {
            $command = new CreateOrderCommand(
                $body->orderNumber ?? '',
                $body->firstName ?? '',
                $body->lastName ?? '',
                $body->address ?? (object) [],
                $body->phone ?? '',
                $body->email ?? '',
                $body->method ?? (object) [],
                $body->items ?? []
            );
        } catch (\InvalidArgumentException $e) {
            return new JsendFailResponse([new JsendError($e->getMessage())], 422);
        }

        try {
            $id = $this->bus->handle($command);
        } catch (ValidationException $e) {
            return new JsendFailResponse([new JsendError($e->getMessage())], 422);
        }

        $body = (object) [
            'id' => $id,
        ];

        return new JsendSuccessResponse($body, 201);
    }
}
