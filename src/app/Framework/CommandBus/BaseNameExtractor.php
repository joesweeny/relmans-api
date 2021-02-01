<?php

namespace Relmans\Framework\CommandBus;

use League\Tactician\Handler\CommandNameExtractor\CommandNameExtractor;

class BaseNameExtractor implements CommandNameExtractor
{
    public function extract($command): string
    {
        return (new \ReflectionClass($command))->getShortName();
    }
}
