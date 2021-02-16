<?php

namespace Relmans\Domain\Enum;

use MyCLabs\Enum\Enum;

/**
 * @method static Measurement EACH()
 * @method static Measurement GRAMS()
 * @method static Measurement HALF()
 * @method static Measurement KILOGRAMS()
 * @method static Measurement LITRE()
 * @method static Measurement TRAY()
 */
class Measurement extends Enum
{
    private const EACH = 'EACH';
    private const GRAMS = 'GRAMS';
    private const HALF = 'HALF';
    private const KILOGRAMS = 'KILOGRAMS';
    private const LITRE = 'LITRE';
    private const TRAY = 'TRAY';
}
