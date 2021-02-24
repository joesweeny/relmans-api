<?php

namespace Relmans\Framework\Routing;

use Relmans\Application\Http\CreateCategoryController;
use Relmans\Application\Http\CreateOrderController;
use Relmans\Application\Http\CreateProductController;
use Relmans\Application\Http\DeleteProductController;
use Relmans\Application\Http\GetOrderController;
use Relmans\Application\Http\HealthCheckController;
use Relmans\Application\Http\ListCategoriesController;
use Relmans\Application\Http\ListOrdersController;
use Relmans\Application\Http\ListProductsController;
use Relmans\Application\Http\UpdateOrderController;
use Relmans\Application\Http\UpdateProductController;
use Relmans\Application\Http\UpdateProductPriceController;
use Slim\App;

class RouteMapper
{
    public function map(App $app): void
    {
        $app->get('/health', HealthCheckController::class);
        $app->post('/category', CreateCategoryController::class);
        $app->get('/category', ListCategoriesController::class);

        $app->post('/product', CreateProductController::class);
        $app->get('/product', ListProductsController::class);
        $app->delete('/product/{id}', DeleteProductController::class);
        $app->patch('/product/{id}', UpdateProductController::class);
        $app->patch('/price/{id}', UpdateProductPriceController::class);

        $app->post('/order', CreateOrderController::class);
        $app->get('/order', ListOrdersController::class);
        $app->get('/order/{id}', GetOrderController::class);
        $app->patch('/order/{id}', UpdateOrderController::class);
    }
}
