<?php

namespace Relmans\Application\Http;

use League\Tactician\CommandBus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Relmans\Boundary\Command\CreateProductCommand;
use Relmans\Framework\Jsend\JsendError;
use Relmans\Framework\Jsend\JsendFailResponse;
use Relmans\Framework\Jsend\JsendSuccessResponse;

class CreateProductController
{
    private CommandBus $commandBus;

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
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
            $command = new CreateProductCommand(
                $body->categoryId ?? '',
                $body->name ?? '',
                $body->status ?? '',
                $body->featured ?? null,
                $body->prices ?? []
            );
        } catch (\UnexpectedValueException $e) {
            return new JsendFailResponse([new JsendError($e->getMessage())], 422);
        }

        $body = (object) [
            'id' => $this->commandBus->handle($command),
        ];

        return new JsendSuccessResponse($body, 201);
    }
}
