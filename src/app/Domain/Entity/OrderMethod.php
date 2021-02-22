<?php

namespace Relmans\Domain\Entity;

class OrderMethod
{
    private string $type;
    private \DateTimeImmutable $date;
    private ?int $fee;

    public function __construct(string $type, \DateTimeImmutable $date, ?int $fee)
    {
        $this->type = $type;
        $this->date = $date;
        $this->fee = $fee;
    }

    public function getType(): string
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