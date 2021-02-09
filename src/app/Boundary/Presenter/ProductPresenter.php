<?php

namespace Relmans\Boundary\Presenter;

use Relmans\Domain\Entity\Product;
use Relmans\Domain\Entity\ProductPrice;

class ProductPresenter
{
    public function toObject(Product $product): object
    {
        $prices = array_map(static function (ProductPrice $price) {
            return (object) [
                'id' => $price->getId()->toString(),
                'value' => $price->getValue(),
                'size' => $price->getSize(),
                'measurement' => $price->getMeasurement()->getValue(),
            ];
        }, $product->getPrices());

        return (object) [
            'id' => $product->getId()->toString(),
            'name' => $product->getName(),
            'categoryId' => $product->getCategoryId()->toString(),
            'status' => $product->getStatus()->getValue(),
            'featured' => $product->isFeatured(),
            'prices' => $prices,
        ];
    }
}
