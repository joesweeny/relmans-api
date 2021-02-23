<?php

namespace Relmans\Domain\Enum;

use MyCLabs\Enum\Enum;

/**
 * @method static FulfilmentType COLLECTION()
 * @method static FulfilmentType DELIVERY()
 */
class FulfilmentType extends Enum
{
    private const COLLECTION = 'COLLECTION';
    private const DELIVERY = 'DELIVERY';
}
