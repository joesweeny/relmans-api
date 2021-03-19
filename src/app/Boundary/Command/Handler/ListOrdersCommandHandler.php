<?php

namespace Relmans\Boundary\Command\Handler;

use Relmans\Boundary\Command\ListOrdersCommand;
use Relmans\Boundary\Presenter\OrderPresenter;
use Relmans\Domain\Entity\Order;
use Relmans\Domain\Persistence\OrderReader;
use Relmans\Domain\Persistence\OrderReaderQuery;
use Relmans\Framework\Time\Clock;

class ListOrdersCommandHandler
{
    private OrderReader $reader;
    private OrderPresenter $presenter;
    /**
     * @var Clock
     */
    private Clock $clock;

    public function __construct(OrderReader $reader, OrderPresenter $presenter, Clock $clock)
    {
        $this->reader = $reader;
        $this->presenter = $presenter;
        $this->clock = $clock;
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
            ->setOrderDateFrom($command->getOrderDateFrom() ?? $this->clock->now()->sub(new \DateInterval('P7D')))
            ->setOrderDateTo($command->getOrderDateTo())
            ->setOrderBy($command->getOrderBy())
            ->setStatus($command->getStatus());

        $orders = $this->reader->get($query);

        return array_map(function (Order $order) {
            return $this->presenter->toObject($order);
        }, $orders);
    }
}
