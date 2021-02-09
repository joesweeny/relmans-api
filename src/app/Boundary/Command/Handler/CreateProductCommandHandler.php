<?php

namespace Relmans\Boundary\Command\Handler;

use Ramsey\Uuid\Uuid;
use Relmans\Boundary\Command\CreateProductCommand;
use Relmans\Domain\Entity\Product;
use Relmans\Domain\Entity\ProductPrice;
use Relmans\Domain\Enum\Measurement;
use Relmans\Domain\Persistence\ProductWriter;
use Relmans\Framework\Time\Clock;

class CreateProductCommandHandler
{
    private ProductWriter $productWriter;
    private Clock $clock;

    public function __construct(ProductWriter $productWriter, Clock $clock)
    {
        $this->productWriter = $productWriter;
        $this->clock = $clock;
    }

    public function handle(CreateProductCommand $command): string
    {
        $productId = Uuid::uuid4();

        $prices = array_map(function (object $price) use ($productId) {
            return new ProductPrice(
                Uuid::uuid4(),
                $productId,
                $price->value,
                $price->size,
                new Measurement($price->measurement),
                $this->clock->now(),
                $this->clock->now()
            );
        }, $command->getPrices());

        $product = new Product(
            $productId,
            $command->getCategoryId(),
            $command->getName(),
            $command->getStatus(),
            $command->isFeatured(),
            $prices,
            $this->clock->now(),
            $this->clock->now()
        );

        $this->productWriter->insert($product);

        return (string) $productId;
    }
}
