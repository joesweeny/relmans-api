<?php

namespace Relmans\Domain\Service\PayPal;

use PHPUnit\Framework\TestCase;
use Relmans\Bootstrap\Config;
use Relmans\Domain\Service\Payment\PaymentService;
use Relmans\Traits\UsesContainer;

class PayPalPaymentServiceTest extends TestCase
{
    use UsesContainer;

    private PaymentService $service;

    public function setUp(): void
    {
        $container = $this->createContainer();
        $driver = $container->get(Config::class)->get('payment.driver');
        $this->service = $container->get(PaymentService::class);
    }

    public function test_getTransactionId_returns_a_string_transaction_id()
    {
        $transactionId = $this->service->getTransactionId('6SU946217H7711020');

        $this->assertEquals('24D980234K635243E', $transactionId);
    }
}
