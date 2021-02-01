<?php

$container = require __DIR__ . '/bootstrap.php';

$console = $container->get(\IntelligenceFusion\Actor\Application\Console\Console::class);

$code = $console->run(
    new \Symfony\Component\Console\Input\ArgvInput(),
    new \Symfony\Component\Console\Output\ConsoleOutput()
);

exit($code);
