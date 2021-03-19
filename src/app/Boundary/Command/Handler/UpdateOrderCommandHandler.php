<?php

namespace Relmans\Boundary\Command\Handler;

use Psr\Log\LoggerInterface;
use Relmans\Boundary\Command\UpdateOrderCommand;
use Relmans\Domain\Enum\FulfilmentType;
use Relmans\Domain\Enum\OrderStatus;
use Relmans\Domain\Persistence\OrderReader;
use Relmans\Domain\Persistence\OrderWriter;
use Relmans\Domain\Persistence\OrderWriterQuery;
use Relmans\Framework\Email\EmailService;
use Relmans\Framework\Exception\EmailException;
use Relmans\Framework\Exception\NotFoundException;

class UpdateOrderCommandHandler
{
    private OrderWriter $writer;
    private OrderReader $reader;
    private EmailService $emailService;
    private LoggerInterface $logger;

    public function __construct(
        OrderWriter $writer,
        OrderReader $reader,
        EmailService $emailService,
        LoggerInterface $logger
    ) {
        $this->writer = $writer;
        $this->reader = $reader;
        $this->emailService = $emailService;
        $this->logger = $logger;
    }

    /**
     * @param UpdateOrderCommand $command
     * @return void
     * @throws NotFoundException
     */
    public function handle(UpdateOrderCommand $command): void
    {
        $query = (new OrderWriterQuery())->setStatus($command->getStatus());

        $this->writer->update($command->getId(), $query);

        if ($command->getStatus() !== null && $command->getStatus()->equals(OrderStatus::PAYMENT_RECEIVED())) {
            $order = $this->reader->getById($command->getId());

            try {
                $this->emailService->sendReceivedEmail($order->getId(), $order->getCustomer()->getEmail());
            } catch (EmailException $e) {
                $this->logger->error("Error sending customer confirmation email: {$e->getMessage()}");
            }
        }

        if ($command->getStatus() !== null && $command->getStatus()->equals(OrderStatus::ACCEPTED())) {
            $order = $this->reader->getById($command->getId());

            try {
                if ($order->getMethod()->getFulfilmentType()->equals(FulfilmentType::DELIVERY())) {
                    $this->emailService->sendDeliveryConfirmation($order);
                }

                if ($order->getMethod()->getFulfilmentType()->equals(FulfilmentType::COLLECTION())) {
                    $this->emailService->sendCollectionConfirmation($order);
                }

                $this->emailService->sendAdminOrderReceivedEmail($order);
            } catch (EmailException $e) {
                $this->logger->error("Error sending customer confirmation email: {$e->getMessage()}");
            }
        }

        if ($command->getStatus() !== null && $command->getStatus()->equals(OrderStatus::CANCELLED())) {
            $this->writer->delete($command->getId());
        }
    }
}
