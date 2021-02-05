<?php

namespace Relmans\Boundary\Command;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class UpdateProductPriceCommand
{
    private UuidInterface $priceId;
    private ?int $value;

    /**
     * @param string $priceId
     * @param int $value
     * @throws \InvalidArgumentException
     */
    public function __construct(string $priceId, ?int $value)
    {
        $this->priceId = Uuid::fromString($priceId);
        $this->value = $this->validateValue($value);
    }

    public function getPriceId(): UuidInterface
    {
        return $this->priceId;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    private function validateValue(?int $value): ?int
    {
        if ($value === null) {
            return null;
        }

        if ($value <= 0) {
            throw new \InvalidArgumentException("'value' field cannot be zero or less");
        }

        return $value;
    }
}
