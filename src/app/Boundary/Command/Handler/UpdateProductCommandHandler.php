<?php

namespace Relmans\Boundary\Command\Handler;

use Relmans\Boundary\Command\UpdateProductCommand;
use Relmans\Domain\Persistence\ProductWriter;
use Relmans\Framework\Exception\NotFoundException;

class UpdateProductCommandHandler
{
    /**
     * @var ProductWriter
     */
    private ProductWriter $productWriter;

    public function __construct(ProductWriter $productWriter)
    {
        $this->productWriter = $productWriter;
    }

    /**
     * @param UpdateProductCommand $command
     * @return void
     * @throws NotFoundException
     */
    public function handle(UpdateProductCommand $command): void
    {
        if ($command->getStatus() === null) {
            return;
        }

        $this->productWriter->updateProductStatus($command->getProductId(), $command->getStatus());
    }
}
