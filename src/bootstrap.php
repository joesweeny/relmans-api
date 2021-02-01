<?php

use Relmans\Bootstrap\ConfigFactory;
use Relmans\Bootstrap\ContainerFactory;

require __DIR__ . '/vendor/autoload.php';

if (file_exists(__DIR__ . '/.env')) {
    (new \josegonzalez\Dotenv\Loader(__DIR__ . '/.env'))
        ->parse()
        ->putenv(true);
}

return (new ContainerFactory())->create(ConfigFactory::create());
