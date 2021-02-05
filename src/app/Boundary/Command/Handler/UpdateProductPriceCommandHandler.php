<?php

namespace Relmans\Boundary\Command\Handler;

use Relmans\Boundary\Command\UpdateProductPriceCommand;
use Relmans\Domain\Persistence\ProductWriter;
use Relmans\Framework\Exception\NotFoundException;

class UpdateProductPriceCommandHandler
{
    private ProductWriter $productWriter;

    public function __construct(ProductWriter $productWriter)
    {
        $this->productWriter = $productWriter;
    }

    /**
     * @param UpdateProductPriceCommand $command
     * @return void
     * @throws NotFoundException
     */
    public function handle(UpdateProductPriceCommand $command): void
    {
        if ($command->getValue() === null) {
            return;
        }

        $this->productWriter->updateProductPrice($command->getPriceId(), $command->getValue());
    }
}
