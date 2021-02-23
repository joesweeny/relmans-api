<?php

namespace Relmans\Domain\Entity;

use Relmans\Domain\Enum\FulfilmentType;

class OrderMethod
{
    private FulfilmentType $type;
    private \DateTimeImmutable $date;
    private ?int $fee;

    public function __construct(FulfilmentType $type, \DateTimeImmutable $date, ?int $fee)
    {
        $this->type = $type;
        $this->date = $date;
        $this->fee = $fee;
    }

    public function getFulfilmentType(): FulfilmentType
    {
        return $this->type;
    }

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function getFee(): ?int
    {
        return $this->fee;
    }
}
