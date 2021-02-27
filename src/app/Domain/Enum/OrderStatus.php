<?php

namespace Relmans\Domain\Enum;

use MyCLabs\Enum\Enum;

/**
 * @method static OrderStatus ACCEPTED()
 * @method static OrderStatus PENDING()
 */
class OrderStatus extends Enum
{
    private const ACCEPTED = 'ACCEPTED';
    private const PENDING = 'PENDING';
}
