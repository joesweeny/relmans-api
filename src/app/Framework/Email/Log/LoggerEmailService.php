<?php

namespace Relmans\Framework\Email\Log;

use Psr\Log\LoggerInterface;
use Relmans\Domain\Entity\Order;
use Relmans\Framework\Email\EmailService;

class LoggerEmailService implements EmailService
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function sendAdminOrderReceivedEmail(Order $order): void
    {
        $this->logger->info("Pretending to send order confirmed to {$order->getCustomer()->getEmail()}");
    }

    public function sendReceivedEmail(string $orderNumber, string $emailAddress): void
    {
        $this->logger->info("Pretending to send order {$orderNumber} received to {$emailAddress}");
    }

    public function sendDeliveryConfirmation(Order $order): void
    {
        $this->logger->info("Pretending to send order confirmed to {$order->getCustomer()->getEmail()}");
    }

    public function sendCollectionConfirmation(Order $order): void
    {
        $this->logger->info("Pretending to send order confirmed to {$order->getCustomer()->getEmail()}");
    }
}
