<?php

namespace Relmans\Boundary\Command;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Relmans\Domain\Enum\Measurement;
use Relmans\Domain\Enum\ProductStatus;

class CreateProductCommand
{
    private UuidInterface $categoryId;
    private string $name;
    private ProductStatus $status;
    private array $prices;

    /**
     * CreateProductCommand constructor.
     * @param string $categoryId
     * @param string $name
     * @param string $status
     * @param array|object[] $prices
     * @throws \UnexpectedValueException
     */
    public function __construct(string $categoryId, string $name, string $status, array $prices)
    {
        $this->validatePrices($prices);
        $this->categoryId = Uuid::fromString($categoryId);
        $this->name = $this->validateName($name);
        $this->status = new ProductStatus($status);
        $this->prices = $prices;
    }

    public function getCategoryId(): UuidInterface
    {
        return $this->categoryId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getStatus(): ProductStatus
    {
        return $this->status;
    }

    /**
     * @return array|object[]
     */
    public function getPrices(): array
    {
        return $this->prices;
    }

    /**
     * @param string $name
     * @return string
     * @throws \UnexpectedValueException
     */
    private function validateName(string $name): string
    {
        if (!$name) {
            throw new \UnexpectedValueException("'name' field is required");
        }

        return $name;
    }

    private function validatePrices(array $prices): void
    {
        foreach ($prices as $price) {
            if (!isset($price->value)) {
                throw new \UnexpectedValueException("'price->value' field is required");
            }

            if (!isset($price->size)) {
                throw new \UnexpectedValueException("'price->size' field is required");
            }

            if (!isset($price->measurement) || !Measurement::isValid($price->measurement)) {
                throw new \UnexpectedValueException("'price->measurement' field is required");
            }
        }
    }
}
