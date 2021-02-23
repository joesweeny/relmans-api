<?php

namespace Relmans\Boundary\Command\Handler;

use Relmans\Boundary\Command\GetOrderCommand;
use Relmans\Boundary\Presenter\OrderPresenter;
use Relmans\Domain\Persistence\OrderReader;
use Relmans\Framework\Exception\NotFoundException;

class GetOrderCommandHandler
{
    private OrderReader $orderReader;
    private OrderPresenter $presenter;

    public function __construct(OrderReader $orderReader, OrderPresenter $presenter)
    {
        $this->orderReader = $orderReader;
        $this->presenter = $presenter;
    }

    /**
     * @param GetOrderCommand $command
     * @return object
     * @throws NotFoundException
     */
    public function handle(GetOrderCommand $command): object
    {
        $order = $this->orderReader->getById($command->getOrderId());

        return $this->presenter->toObject($order);
    }
}
