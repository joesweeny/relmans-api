<?php

namespace Relmans\Domain\Persistence;

use Relmans\Domain\Enum\ProductStatus;

class ProductWriterQuery
{
    private ?string $name;
    private ?ProductStatus $status;
    private ?bool $isFeatured;

    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name ?? null;
    }

    public function setStatus(?ProductStatus $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getStatus(): ?ProductStatus
    {
        return $this->status ?? null;
    }

    public function setIsFeatured(?bool $isFeatured): self
    {
        $this->isFeatured = $isFeatured;
        return $this;
    }

    public function getIsFeatured(): ?bool
    {
        return $this->isFeatured ?? null;
    }
}
