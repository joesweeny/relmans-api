<?php

namespace Relmans\Framework\Time;

use DateTimeImmutable;
use DateTimeZone;

class SystemClock implements Clock
{
    private DateTimeZone $timeZone;

    public function __construct(DateTimeZone $timeZone = null)
    {
        $this->timeZone = $timeZone ?? new DateTimeZone(date_default_timezone_get());
    }

    public function now(): DateTimeImmutable
    {
        return new DateTimeImmutable('now', $this->timeZone);
    }
}
