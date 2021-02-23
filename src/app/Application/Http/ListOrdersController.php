<?php

namespace Relmans\Application\Http;

use League\Tactician\CommandBus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Relmans\Boundary\Command\ListOrdersCommand;
use Relmans\Framework\Jsend\JsendError;
use Relmans\Framework\Jsend\JsendFailResponse;
use Relmans\Framework\Jsend\JsendSuccessResponse;

class ListOrdersController
{
    private CommandBus $commandBus;

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $params = (object) $request->getQueryParams();

        try {
            $command = new ListOrdersCommand(
                $params->postCode ?? null,
                $params->orderNumber ?? null,
                $params->deliveryFrom ?? null,
                $params->deliveryTo ?? null,
                $params->orderFrom ?? null,
                $params->orderTo ?? null,
                $params->orderBy ?? null,
            );
        } catch (\InvalidArgumentException $e) {
            return new JsendFailResponse([new JsendError($e->getMessage())], 422);
        }

        $body = (object) [
            'orders' => $this->commandBus->handle($command),
        ];

        return new JsendSuccessResponse($body, 200);
    }
}
