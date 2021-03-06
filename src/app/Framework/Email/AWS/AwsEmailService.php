<?php

namespace Relmans\Framework\Email\AWS;

use Aws\Exception\AwsException;
use Aws\Ses\SesClient;
use Relmans\Domain\Entity\Order;
use Relmans\Domain\Entity\OrderItem;
use Relmans\Framework\Email\EmailService;
use Relmans\Framework\Exception\EmailException;

class AwsEmailService implements EmailService
{
    private SesClient $client;

    public function __construct(SesClient $client)
    {
        $this->client = $client;
    }

    public function sendAdminOrderReceivedEmail(Order $order): void
    {
        $customer = $order->getCustomer();

        $address = $customer->getAddress() !== null
            ? array_filter((array) $customer->getAddress()->jsonSerialize())
            : null;

        $total = array_reduce($order->getItems(), static function ($carry, OrderItem $item) {
            return $carry + ($item->getPrice() * $item->getQuantity() / 100);
        });

        $data = (object) [
            'orderID' => $order->getId(),
            'date' => $order->getMethod()->getDate()->format('l jS F Y'),
            'address' => $address !== null ? implode('<br>', $address) : null,
            'method' => $order->getMethod()->getFulfilmentType()->getValue(),
            'total' => number_format($total, 2),
        ];

        $config = [
            'Destination' => [
                'ToAddresses' => ['orders@relmans.co.uk'],
            ],
            'Source' => 'Relmans <orders@relmans.co.uk>',
            'Template' => 'OrderReceivedAdmin',
            'TemplateData' => json_encode($data),
        ];

        try {
            $this->client->sendTemplatedEmail($config);
        } catch (AwsException $e) {
            throw new EmailException($e->getMessage());
        }
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

        $address = $customer->getAddress() !== null
            ? array_filter((array) $customer->getAddress()->jsonSerialize())
            : null;

        $data = (object) [
            'orderID' => $order->getId(),
            'date' => $order->getMethod()->getDate()->format('l jS F Y'),
            'address' => $address !== null ? implode('<br>', $address) : null,
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
            'orderID' => $order->getId(),
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
