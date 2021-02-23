<?php

namespace Relmans\Domain\Service\Payment;

interface PaymentService
{
    /**
     * @param string $orderId
     * @return string
     * @return string
     * @throws PaymentServiceException
     */
    public function getTransactionId(string $orderId): string;
}
