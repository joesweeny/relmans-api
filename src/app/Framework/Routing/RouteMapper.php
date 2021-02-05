<?php

namespace Relmans\Framework\Routing;

use Relmans\Application\Http\CreateProductController;
use Relmans\Application\Http\HealthCheckController;
use Relmans\Application\Http\ListCategoriesController;
use Relmans\Application\Http\ListProductsController;
use Slim\App;

class RouteMapper
{
    public function map(App $app): void
    {
        $app->get('/health', HealthCheckController::class);
        $app->get('/category', ListCategoriesController::class);
        $app->post('/product', CreateProductController::class);
        $app->get('/product', ListProductsController::class);
    }
}
