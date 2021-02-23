<?php

namespace Relmans\Framework\Email;

use Relmans\Domain\Entity\Order;
use Relmans\Framework\Exception\EmailException;

interface EmailService
{
    /**
     * @param string $orderNumber
     * @param string $emailAddress
     * @return void
     * @throws EmailException
     */
    public function sendReceivedEmail(string $orderNumber, string $emailAddress): void;

    /**
     * @param Order $order
     * @return void
     * @throws EmailException
     */
    public function sendDeliveryConfirmation(Order $order): void;

    /**
     * @param Order $order
     * @return void
     * @throws EmailException
     */
    public function sendCollectionConfirmation(Order $order): void;
}
