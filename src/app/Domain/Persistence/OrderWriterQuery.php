<?php

namespace Relmans\Domain\Persistence;

use Relmans\Domain\Enum\OrderStatus;

class OrderWriterQuery
{
    private ?OrderStatus $status;

    public function setStatus(OrderStatus $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getStatus(): ?OrderStatus
    {
        return $this->status ?? null;
    }
}
