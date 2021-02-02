<?php

namespace Relmans\Domain\Enum;

use MyCLabs\Enum\Enum;

/**
 * @method static Measurement EACH()
 * @method static Measurement GRAMS()
 * @method static Measurement KILOGRAMS()
 */
class Measurement extends Enum
{
    private const EACH = 'EACH';
    private const GRAMS = 'GRAMS';
    private const KILOGRAMS = 'KILOGRAMS';
}
