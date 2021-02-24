<?php

namespace Relmans\Boundary\Command\Handler;

use Relmans\Boundary\Command\ListOrdersCommand;
use Relmans\Boundary\Presenter\OrderPresenter;
use Relmans\Domain\Entity\Order;
use Relmans\Domain\Persistence\OrderReader;
use Relmans\Domain\Persistence\OrderReaderQuery;

class ListOrdersCommandHandler
{
    private OrderReader $reader;
    private OrderPresenter $presenter;

    public function __construct(OrderReader $reader, OrderPresenter $presenter)
    {
        $this->reader = $reader;
        $this->presenter = $presenter;
    }

    /**
     * @param ListOrdersCommand $command
     * @return array|object[]
     */
    public function handle(ListOrdersCommand $command): array
    {
        $query = (new OrderReaderQuery())
            ->setPostCode($command->getPostCode())
            ->setDeliveryDateFrom($command->getDeliveryFrom())
            ->setDeliveryDateTo($command->getDeliveryTo())
            ->setOrderDateFrom($command->getOrderDateFrom())
            ->setOrderDateTo($command->getOrderDateTo())
            ->setOrderBy($command->getOrderBy());

        $orders = $this->reader->get($query);

        return array_map(function (Order $order) {
            return $this->presenter->toObject($order);
        }, $orders);
    }
}
