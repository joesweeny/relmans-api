<?php

namespace Relmans\Application\Http;

use GuzzleHttp\Psr7\Response;
use League\Tactician\CommandBus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Relmans\Boundary\Command\DeleteOrderCommand;

class DeleteOrderController
{
    private CommandBus $bus;

    public function __construct(CommandBus $bus)
    {
        $this->bus = $bus;
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $command = new DeleteOrderCommand($request->getAttribute('id', ''));

        $this->bus->handle($command);

        return new Response(204);
    }
}
