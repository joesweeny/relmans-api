<?php

namespace Relmans\Boundary\Command\Handler;

use Relmans\Boundary\Command\CreateOrderCommand;
use Relmans\Domain\Factory\OrderFactory;
use Relmans\Domain\Persistence\OrderWriter;
use Relmans\Framework\Exception\ValidationException;

class CreateOrderCommandHandler
{
    private OrderFactory $factory;
    private OrderWriter $writer;

    public function __construct(OrderFactory $factory, OrderWriter $writer)
    {
        $this->factory = $factory;
        $this->writer = $writer;
    }

    /**
     * @param CreateOrderCommand $command
     * @return string
     * @throws ValidationException
     */
    public function handle(CreateOrderCommand $command): string
    {
        $order = $this->factory->createNewOrder(
            $command->getOrderNumber(),
            $command->getCustomer(),
            $command->getMethod(),
            $command->getItems()
        );

        $this->writer->insert($order);

        return $order->getId();
    }
}
