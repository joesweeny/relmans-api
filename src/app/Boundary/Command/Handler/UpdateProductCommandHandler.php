<?php

namespace Relmans\Boundary\Command\Handler;

use Relmans\Boundary\Command\UpdateProductCommand;
use Relmans\Domain\Persistence\ProductWriter;
use Relmans\Domain\Persistence\ProductWriterQuery;
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
        $query = (new ProductWriterQuery())
            ->setStatus($command->getStatus())
            ->setIsFeatured($command->getFeatured());

        $this->productWriter->updateProduct($command->getProductId(), $query);
    }
}
