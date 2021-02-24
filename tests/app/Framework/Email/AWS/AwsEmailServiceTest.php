<?php

namespace Relmans\Framework\Email\AWS;

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Relmans\Bootstrap\Config;
use Relmans\Domain\Entity\Address;
use Relmans\Domain\Entity\Customer;
use Relmans\Domain\Entity\Order;
use Relmans\Domain\Entity\OrderItem;
use Relmans\Domain\Entity\OrderMethod;
use Relmans\Domain\Enum\FulfilmentType;
use Relmans\Domain\Enum\Measurement;
use Relmans\Domain\Enum\OrderStatus;
use Relmans\Framework\Email\EmailService;
use Relmans\Traits\UsesContainer;

class AwsEmailServiceTest extends TestCase
{
    use UsesContainer;

    private EmailService $service;

    public function setUp(): void
    {
        $container = $this->createContainer();
        $driver  = $container->get(Config::class)->get('email.driver');

        if ($driver !== 'aws') {
            $this->markTestSkipped('AWS email driver is required to run this test suite');
        }

        $this->service = $container->get(EmailService::class);
    }

    public function test_sendAdminOrderReceivedEmail_sends_an_email_via_aws_ses_client()
    {
        $this->service->sendAdminOrderReceivedEmail($this->order());
    }

    public function test_sendReceivedEmail_sends_an_email_via_aws_ses_client()
    {
        $this->service->sendReceivedEmail('YU9KAJSAKJ3', 'orders@relmans.co.uk');
    }

    public function test_sendDeliveryConfirmation_sends_an_email_via_aws_ses_client()
    {
        $this->service->sendDeliveryConfirmation($this->order());
    }

    public function test_sendCollectionConfirmation_sends_an_email_via_aws_ses_client()
    {
        $this->service->sendCollectionConfirmation($this->order());
    }

    private function order(): Order
    {
        $orderId = 'YU9KAJSAKJ3';
        $transactionId = 'ID9991111';
        $address = new Address(
            '58 Holwick Close',
            'Templetown',
            null,
            'Consett',
            null,
            'DH87UJ'
        );
        $customer = new Customer(
            'Joe',
            'Sweeny',
            $address,
            '07939843048',
            'orders@relmans.co.uk'
        );
        $status = OrderStatus::CONFIRMED();
        $method = new OrderMethod(
            FulfilmentType::DELIVERY(),
            new \DateTimeImmutable('2021-03-12T15:00:00+00'),
            250
        );
        $createdAt = new \DateTimeImmutable();
        $updatedAt = new \DateTimeImmutable();

        return new Order(
            $orderId,
            $transactionId,
            $customer,
            $status,
            $method,
            [
                new OrderItem(
                    Uuid::uuid4(),
                    $orderId,
                    Uuid::uuid4(),
                    'Cabbage',
                    100,
                    1,
                    Measurement::EACH(),
                    100,
                    new \DateTimeImmutable(),
                    new \DateTimeImmutable(),
                ),
                new OrderItem(
                    Uuid::uuid4(),
                    $orderId,
                    Uuid::uuid4(),
                    'Cabbage',
                    500,
                    1,
                    Measurement::EACH(),
                    100,
                    new \DateTimeImmutable(),
                    new \DateTimeImmutable(),
                )
            ],
            $createdAt,
            $updatedAt
        );
    }
}
