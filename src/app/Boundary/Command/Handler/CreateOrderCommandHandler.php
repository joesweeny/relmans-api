<?php

namespace Relmans\Boundary\Command\Handler;

use Psr\Log\LoggerInterface;
use Relmans\Boundary\Command\CreateOrderCommand;
use Relmans\Domain\Factory\OrderFactory;
use Relmans\Domain\Persistence\OrderWriter;
use Relmans\Framework\Email\EmailService;
use Relmans\Framework\Exception\EmailException;
use Relmans\Framework\Exception\ValidationException;

class CreateOrderCommandHandler
{
    private OrderFactory $factory;
    private OrderWriter $writer;
    private EmailService $emailService;
    private LoggerInterface $logger;

    public function __construct(
        OrderFactory $factory,
        OrderWriter $writer,
        EmailService $emailService,
        LoggerInterface $logger
    ) {
        $this->factory = $factory;
        $this->writer = $writer;
        $this->emailService = $emailService;
        $this->logger = $logger;
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

        try {
            $this->emailService->sendAdminOrderReceivedEmail($order);
        } catch (EmailException $e) {
            $this->logger->error(
                "Error sending customer order confirmation email: {$e->getMessage()}",
                [
                    'exception' => $e,
                    'message' => $e->getMessage(),
                ]
            );
        }

        return $order->getId();
    }
}
