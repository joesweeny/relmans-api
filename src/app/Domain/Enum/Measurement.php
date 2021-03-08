<?php

namespace Relmans\Domain\Enum;

use MyCLabs\Enum\Enum;

/**
 * @method static Measurement BAG()
 * @method static Measurement BUNCH()
 * @method static Measurement BOX()
 * @method static Measurement EACH()
 * @method static Measurement GRAMS()
 * @method static Measurement HALF()
 * @method static Measurement KILOGRAMS()
 * @method static Measurement LITRE()
 * @method static Measurement PACK()
 * @method static Measurement PUNNET()
 * @method static Measurement SACK()
 * @method static Measurement TRAY()
 */
class Measurement extends Enum
{
    private const BAG = 'BAG';
    private const BUNCH = 'BUNCH';
    private const BOX = 'BOX';
    private const EACH = 'EACH';
    private const GRAMS = 'GRAMS';
    private const HALF = 'HALF';
    private const KILOGRAMS = 'KILOGRAMS';
    private const LITRE = 'LITRE';
    private const PACK = 'PACK';
    private const PUNNET = 'PUNNET';
    private const SACK = 'SACK';
    private const TRAY = 'TRAY';
}
