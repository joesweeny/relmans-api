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
    private bool $featured;

    /**
     * @param string $categoryId
     * @param string $name
     * @param string $status
     * @param ?bool $featured
     * @param array|object[] $prices
     * @throws \UnexpectedValueException
     */
    public function __construct(string $categoryId, string $name, string $status, ?bool $featured, array $prices)
    {
        $this->validatePrices($prices);
        $this->categoryId = Uuid::fromString($categoryId);
        $this->name = $this->validateName($name);
        $this->status = new ProductStatus($status);
        $this->featured = $this->validateFeatured($featured);
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

    public function isFeatured(): bool
    {
        return $this->featured;
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

    private function validateFeatured(?bool $featured): bool
    {
        if (!is_bool($featured)) {
            throw new \UnexpectedValueException("'featured' field is required");
        }

        return $featured;
    }
}
