<?php

namespace Relmans\Domain\Persistence;

class OrderReaderQuery
{
    private ?string $postCode;
    private ?string $orderNumber;
    private ?\DateTimeImmutable $deliveryFrom;
    private ?\DateTimeImmutable $deliveryTo;
    private ?\DateTimeImmutable $orderFrom;
    private ?\DateTimeImmutable $orderTo;
    private ?string $orderBy;

    public function setPostCode(?string $postCode): self
    {
        $this->postCode = $postCode;
        return $this;
    }

    public function getPostCode(): ?string
    {
        return $this->postCode ?? null;
    }

    public function setOrderNumber(?string $orderNumber): self
    {
        $this->orderNumber = $orderNumber;
        return $this;
    }

    public function getOrderNumber(): ?string
    {
        return $this->orderNumber ?? null;
    }

    public function setDeliveryDateFrom(?\DateTimeImmutable $date): self
    {
        $this->deliveryFrom = $date;
        return $this;
    }

    public function getDeliveryDateFrom(): ?\DateTimeImmutable
    {
        return $this->deliveryFrom ?? null;
    }

    public function setDeliveryDateTo(?\DateTimeImmutable $date): self
    {
        $this->deliveryTo = $date;
        return $this;
    }

    public function getDeliveryDateTo(): ?\DateTimeImmutable
    {
        return $this->deliveryTo ?? null;
    }

    public function setOrderDateFrom(?\DateTimeImmutable $date): self
    {
        $this->orderFrom = $date;
        return $this;
    }

    public function getOrderDateFrom(): ?\DateTimeImmutable
    {
        return $this->orderFrom ?? null;
    }

    public function setOrderDateTo(?\DateTimeImmutable $date): self
    {
        $this->orderTo = $date;
        return $this;
    }

    public function getOrderDateTo(): ?\DateTimeImmutable
    {
        return $this->orderTo ?? null;
    }

    public function setOrderBy(?string $order): self
    {
        $this->orderBy = $order;
        return $this;
    }

    public function getOrderBy(): ?string
    {
        return $this->orderBy ?? null;
    }
}
