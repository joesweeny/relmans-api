<?php

namespace Relmans\Boundary\Command\Handler;

use Relmans\Boundary\Command\DeleteOrderCommand;
use Relmans\Domain\Persistence\OrderWriter;

class DeleteOrderCommandHandler
{
    private OrderWriter $orderWriter;

    public function __construct(OrderWriter $orderWriter)
    {
        $this->orderWriter = $orderWriter;
    }

    public function handle(DeleteOrderCommand $command): void
    {
        $this->orderWriter->delete($command->getOrderId());
    }
}
