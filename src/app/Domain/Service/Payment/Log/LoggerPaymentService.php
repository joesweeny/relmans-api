<?php

namespace Relmans\Domain\Service\Payment\Log;

use Psr\Log\LoggerInterface;
use Relmans\Domain\Service\Payment\PaymentService;

class LoggerPaymentService implements PaymentService
{
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function getTransactionId(string $orderId): string
    {
        $this->logger->info("Generating random string in logger payment service");
        return uniqid('ORDER', true);
    }
}
