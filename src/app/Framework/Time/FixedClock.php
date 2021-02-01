<?php

namespace Relmans\Framework\Time;

use DateTimeImmutable;

class FixedClock implements Clock
{
    private DateTimeImmutable $currentDatetime;

    public function __construct(DateTimeImmutable $currentDatetime = null)
    {
        $this->currentDatetime = $currentDatetime ?? new DateTimeImmutable();
    }

    public function now(): DateTimeImmutable
    {
        return $this->currentDatetime;
    }
}
