<?php

namespace Relmans\Boundary\Command\Handler;

use Relmans\Boundary\Command\ListProductsCommand;
use Relmans\Boundary\Presenter\ProductPresenter;
use Relmans\Domain\Entity\Product;
use Relmans\Domain\Persistence\ProductReader;
use Relmans\Domain\Persistence\ProductReaderQuery;

class ListProductsCommandHandler
{
    private ProductReader $reader;
    private ProductPresenter $presenter;

    public function __construct(ProductReader $reader, ProductPresenter $presenter)
    {
        $this->reader = $reader;
        $this->presenter = $presenter;
    }

    public function handle(ListProductsCommand $command): array
    {
        $query = (new ProductReaderQuery())
            ->setCategoryId($command->getCategoryId())
            ->setSearchTerm($command->getSearch())
            ->setOrderBy($command->getOrderBy());

        $products = $this->reader->get($query);

        return array_map(function (Product $product) {
            return $this->presenter->toObject($product);
        }, $products);
    }
}
