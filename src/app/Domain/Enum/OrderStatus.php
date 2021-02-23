<?php

namespace Relmans\Domain\Enum;

use MyCLabs\Enum\Enum;

/**
 * @method static OrderStatus CANCELLED()
 * @method static OrderStatus CONFIRMED()
 * @method static OrderStatus PENDING()
 */
class OrderStatus extends Enum
{
    private const CANCELLED = 'CANCELLED';
    private const CONFIRMED = 'CONFIRMED';
    private const PENDING = 'PENDING';
}
