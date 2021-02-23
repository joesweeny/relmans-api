<?php

namespace Relmans\Domain\Service\Payment;

use Relmans\Framework\Exception\NotFoundException;

interface PaymentService
{
    /**
     * @param string $orderId
     * @return string
     * @return string
     * @throws PaymentServiceException
     * @throws NotFoundException
     */
    public function getTransactionId(string $orderId): string;
}
