<?php

namespace Relmans\Application\Http;

use League\Tactician\CommandBus;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Relmans\Boundary\Command\ListProductsCommand;
use Relmans\Framework\Jsend\JsendError;
use Relmans\Framework\Jsend\JsendFailResponse;
use Relmans\Framework\Jsend\JsendSuccessResponse;

class ListProductsController
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
            $command = new ListProductsCommand(
                $params->categoryId ?? null,
                $params->search ?? null,
                $params->order ?? null,
            );
        } catch (\InvalidArgumentException $e) {
            return new JsendFailResponse([new JsendError($e->getMessage())], 422);
        }

        $body = (object) [
            'products' => $this->commandBus->handle($command),
        ];

        return new JsendSuccessResponse($body, 200);
    }
}
