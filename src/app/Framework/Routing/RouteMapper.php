<?php

namespace Relmans\Framework\Routing;

use Relmans\Application\Http\HealthCheckController;
use Relmans\Application\Http\ListCategoriesController;
use Slim\App;

class RouteMapper
{
    public function map(App $app): void
    {
        $app->get('/health', HealthCheckController::class);
        $app->get('/category', ListCategoriesController::class);
    }
}
