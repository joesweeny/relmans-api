<?php

namespace Relmans\Boundary\Command\Handler;

use Relmans\Boundary\Command\DeleteProductCommand;
use Relmans\Domain\Persistence\ProductWriter;

class DeleteProductCommandHandler
{
    private ProductWriter $productWriter;

    public function __construct(ProductWriter $productWriter)
    {
        $this->productWriter = $productWriter;
    }

    public function handle(DeleteProductCommand $command): void
    {
        $this->productWriter->delete($command->getProductId());
    }
}
