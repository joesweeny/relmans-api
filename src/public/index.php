<?php

$container = require __DIR__ . '/../bootstrap.php';

$container->get(\Slim\App::class)->run();
