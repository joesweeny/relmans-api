<?php

namespace Relmans\Application\Http;

use GuzzleHttp\Psr7\Response;
use League\Tactician\CommandBus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Relmans\Boundary\Command\DeleteProductCommand;
use Relmans\Framework\Jsend\JsendError;
use Relmans\Framework\Jsend\JsendFailResponse;

class DeleteProductController
{
    private CommandBus $bus;

    public function __construct(CommandBus $bus)
    {
        $this->bus = $bus;
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $command = new DeleteProductCommand($request->getAttribute('id', ''));
        } catch (\InvalidArgumentException $e) {
            return new JsendFailResponse([new JsendError($e->getMessage())], 404);
        }

        $this->bus->handle($command);

        return new Response(204);
    }
}
