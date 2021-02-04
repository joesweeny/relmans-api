<?php

namespace Relmans\Domain\Enum;

use MyCLabs\Enum\Enum;

/**
 * @method static Measurement BAG()
 * @method static Measurement BUNCH()
 * @method static Measurement EACH()
 * @method static Measurement GRAMS()
 * @method static Measurement HALF()
 * @method static Measurement KILOGRAMS()
 * @method static Measurement PACK()
 * @method static Measurement PUNNET()
 * @method static Measurement TRAY()
 */
class Measurement extends Enum
{
    private const BAG = 'BAG';
    private const BUNCH = 'BAG';
    private const EACH = 'EACH';
    private const GRAMS = 'GRAMS';
    private const HALF = 'HALF';
    private const KILOGRAMS = 'KILOGRAMS';
    private const PACK = 'PACK';
    private const PUNNET = 'PUNNET';
    private const TRAY = 'TRAY';
}
