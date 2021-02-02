<?php

namespace Relmans\Domain\Enum;

use MyCLabs\Enum\Enum;

/**
 * @method static ProductStatus IN_STOCK()
 * @method static ProductStatus OUT_OF_SEASON()
 * @method static ProductStatus OUT_OF_STOCK()
 */
class ProductStatus extends Enum
{
    private const IN_STOCK = 'IN_STOCK';
    private const OUT_OF_SEASON = 'OUT_OF_SEASON';
    private const OUT_OF_STOCK = 'OUT_OF_STOCK';
}
