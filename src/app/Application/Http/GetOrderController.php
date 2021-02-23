<?php

namespace Relmans\Application\Http;

use League\Tactician\CommandBus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Relmans\Boundary\Command\GetOrderCommand;
use Relmans\Framework\Exception\NotFoundException;
use Relmans\Framework\Jsend\JsendError;
use Relmans\Framework\Jsend\JsendFailResponse;
use Relmans\Framework\Jsend\JsendSuccessResponse;

class GetOrderController
{
    private CommandBus $bus;

    public function __construct(CommandBus $bus)
    {
        $this->bus = $bus;
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $command = new GetOrderCommand($request->getAttribute('id', ''));
        } catch (\InvalidArgumentException $e) {
            return new JsendFailResponse([new JsendError($e->getMessage())], 404);
        }

        try {
            $order = $this->bus->handle($command);
        } catch (NotFoundException $e) {
            return new JsendFailResponse([new JsendError($e->getMessage())], 404);
        }

        $body = (object) [
            'order' => $order,
        ];

        return new JsendSuccessResponse($body, 200);
    }
}
