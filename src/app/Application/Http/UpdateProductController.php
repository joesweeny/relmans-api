<?php

namespace Relmans\Application\Http;

use GuzzleHttp\Psr7\Response;
use League\Tactician\CommandBus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Relmans\Boundary\Command\UpdateProductCommand;
use Relmans\Framework\Exception\NotFoundException;
use Relmans\Framework\Jsend\JsendError;
use Relmans\Framework\Jsend\JsendFailResponse;

class UpdateProductController
{
    /**
     * @var CommandBus
     */
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
            $command = new UpdateProductCommand(
                $request->getAttribute('id', ''),
                $body->status ?? null
            );
        } catch (\InvalidArgumentException $e) {
            return new JsendFailResponse([new JsendError($e->getMessage())], 404);
        } catch (\UnexpectedValueException $e) {
            return new JsendFailResponse([new JsendError($e->getMessage())], 422);
        }

        try {
            $this->commandBus->handle($command);
        } catch (NotFoundException $e) {
            return new JsendFailResponse([new JsendError($e->getMessage())], 404);
        }

        return new Response(204);
    }
}
