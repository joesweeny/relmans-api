<?php

namespace Relmans\Boundary\Command;

use Relmans\Domain\Enum\OrderStatus;

class UpdateOrderCommand
{
    private string $id;
    private ?OrderStatus $status;

    public function __construct(string $id, ?string $status)
    {
        $this->id = $id;
        $this->status = $status !== null ? new OrderStatus($status) : null;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getStatus(): ?OrderStatus
    {
        return $this->status;
    }
}
