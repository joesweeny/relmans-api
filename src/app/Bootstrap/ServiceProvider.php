<?php

namespace Relmans\Bootstrap;

use DI\Definition\Helper\DefinitionHelper;

interface ServiceProvider
{
    /**
     * @return array|DefinitionHelper[]
     */
    public function getDefinitions(): array;
}
