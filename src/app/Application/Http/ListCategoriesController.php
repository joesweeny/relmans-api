<?php

namespace Relmans\Application\Http;

use League\Tactician\CommandBus;
use Relmans\Boundary\Command\ListCategoriesCommand;
use Relmans\Framework\Jsend\JsendSuccessResponse;

class ListCategoriesController
{
    private CommandBus $commandBus;

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function __invoke()
    {
        $categories = $this->commandBus->handle(new ListCategoriesCommand());

        $data = (object) [
            'categories' => $categories,
        ];

        return new JsendSuccessResponse($data, '200');
    }
}
