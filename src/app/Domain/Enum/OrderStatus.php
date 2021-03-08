<?php

namespace Relmans\Domain\Enum;

use MyCLabs\Enum\Enum;

/**
 * @method static OrderStatus ACCEPTED()
 * @method static OrderStatus PAYMENT_RECEIVED()
 * @method static OrderStatus PENDING()
 */
class OrderStatus extends Enum
{
    private const ACCEPTED = 'ACCEPTED';
    private const PAYMENT_RECEIVED = 'PAYMENT_RECEIVED';
    private const PENDING = 'PENDING';
}
