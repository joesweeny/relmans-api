<?php

namespace Relmans\Framework\Email\AWS;

use Aws\Exception\AwsException;
use Aws\Ses\SesClient;
use Relmans\Domain\Entity\Order;
use Relmans\Framework\Email\EmailService;
use Relmans\Framework\Exception\EmailException;

class AwsEmailService implements EmailService
{
    private SesClient $client;

    public function __construct(SesClient $client)
    {
        $this->client = $client;
    }

    public function sendReceivedEmail(string $orderNumber, string $emailAddress): void
    {
        $data = (object) [
            'orderID' => $orderNumber,
        ];

        $config = [
            'Destination' => [
                'ToAddresses' => [$emailAddress],
            ],
            'Source' => 'Relmans <orders@relmans.co.uk>',
            'Template' => 'OrderReceived',
            'TemplateData' => json_encode($data),
        ];

        try {
            $this->client->sendTemplatedEmail($config);
        } catch (AwsException $e) {
            throw new EmailException($e->getMessage());
        }
    }

    public function sendDeliveryConfirmation(Order $order): void
    {
        $customer = $order->getCustomer();

        $address = array_filter((array) $customer->getAddress()->jsonSerialize());

        $data = (object) [
            'orderID' => $order->getExternalId(),
            'date' => $order->getMethod()->getDate()->format('l jS F Y'),
            'address' => implode('<br>', $address),
        ];

        $config = [
            'Destination' => [
                'ToAddresses' => [$customer->getEmail()],
            ],
            'Source' => 'Relmans <orders@relmans.co.uk>',
            'Template' => 'OrderConfirmedDelivery',
            'TemplateData' => json_encode($data),
        ];

        try {
            $this->client->sendTemplatedEmail($config);
        } catch (AwsException $e) {
            throw new EmailException($e->getMessage());
        }
    }

    public function sendCollectionConfirmation(Order $order): void
    {
        $customer = $order->getCustomer();

        $data = (object) [
            'orderID' => $order->getExternalId(),
            'date' => $order->getMethod()->getDate()->format('l jS F Y'),
            'time' => $order->getMethod()->getDate()->format('g:iA'),
        ];

        $config = [
            'Destination' => [
                'ToAddresses' => [$customer->getEmail()],
            ],
            'Source' => 'Relmans <orders@relmans.co.uk>',
            'Template' => 'OrderConfirmedCollection',
            'TemplateData' => json_encode($data),
        ];

        try {
            $this->client->sendTemplatedEmail($config);
        } catch (AwsException $e) {
            throw new EmailException($e->getMessage());
        }
    }
}
