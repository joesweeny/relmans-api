<?php

namespace Relmans\Framework\Routing;

use Relmans\Application\Http\HealthCheckController;
use Slim\App;

class RouteMapper
{
    public function map(App $app): void
    {
        $app->get('/health', HealthCheckController::class);
    }
}
