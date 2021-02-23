<?php

namespace Relmans\Boundary\Command;

class ListOrdersCommand
{
    private ?string $postCode;
    private ?string $orderNumber;
    private ?\DateTimeImmutable $deliveryFrom;
    private ?\DateTimeImmutable $deliveryTo;
    private ?\DateTimeImmutable $orderDateFrom;
    private ?\DateTimeImmutable $orderDateTo;
    private ?string $orderBy;

    /**
     * @param string|null $postCode
     * @param string|null $orderNumber
     * @param string|null $deliveryFrom
     * @param string|null $deliveryTo
     * @param string|null $orderDateFrom
     * @param string|null $orderDateTo
     * @param string|null $orderBy
     * @throws \Exception
     */
    public function __construct(
        ?string $postCode,
        ?string $orderNumber,
        ?string $deliveryFrom,
        ?string $deliveryTo,
        ?string $orderDateFrom,
        ?string $orderDateTo,
        ?string $orderBy
    ) {
        $this->postCode = $postCode;
        $this->orderNumber = $orderNumber;
        $this->deliveryFrom = $deliveryFrom !== null ? new \DateTimeImmutable($deliveryFrom) : null;
        $this->deliveryTo = $deliveryTo !== null ? new \DateTimeImmutable($deliveryTo) : null;
        $this->orderDateFrom = $orderDateFrom !== null ? new \DateTimeImmutable($orderDateFrom) : null;
        $this->orderDateTo = $orderDateTo !== null ? new \DateTimeImmutable($orderDateTo) : null;
        $this->orderBy = $orderBy;
    }

    public function getPostCode(): ?string
    {
        return $this->postCode;
    }

    public function getOrderNumber(): ?string
    {
        return $this->orderNumber;
    }

    public function getDeliveryFrom(): ?\DateTimeImmutable
    {
        return $this->deliveryFrom;
    }

    public function getDeliveryTo(): ?\DateTimeImmutable
    {
        return $this->deliveryTo;
    }

    public function getOrderDateFrom(): ?\DateTimeImmutable
    {
        return $this->orderDateFrom;
    }

    public function getOrderDateTo(): ?\DateTimeImmutable
    {
        return $this->orderDateTo;
    }

    public function getOrderBy(): ?string
    {
        return $this->orderBy;
    }
}
