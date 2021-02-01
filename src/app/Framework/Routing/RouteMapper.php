<?php

namespace IntelligenceFusion\Actor\Framework\Routing;

use IntelligenceFusion\Actor\Application\Http\HealthCheckController;
use Slim\App;

class RouteMapper
{
    public function map(App $app): void
    {
        $app->get('/health', HealthCheckController::class);
    }
}
