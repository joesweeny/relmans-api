<?php

namespace Relmans\Framework\Time;

interface Clock
{
    public function now(): \DateTimeImmutable;
}
