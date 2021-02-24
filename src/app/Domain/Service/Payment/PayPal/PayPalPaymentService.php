<?php

namespace Relmans\Domain\Service\Payment\PayPal;

use Http\Discovery\Exception\NotFoundException;
use PayPalCheckoutSdk\Orders\OrdersGetRequest;
use PayPalHttp\HttpClient;
use PayPalHttp\HttpException;
use Relmans\Domain\Service\Payment\PaymentService;
use Relmans\Domain\Service\Payment\PaymentServiceException;

class PayPalPaymentService implements PaymentService
{
    private HttpClient $client;

    public function __construct(HttpClient $client)
    {
        $this->client = $client;
    }

    public function getTransactionId(string $orderId): string
    {
        try {
            $response = $this->client->execute(new OrdersGetRequest($orderId));
        } catch (HttpException $e) {
            $error = json_decode($e->getMessage());

            if ($error->name === 'RESOURCE_NOT_FOUND') {
                throw new NotFoundException("Order {$orderId} does not exist");
            }

            throw new PaymentServiceException("Error: {$error->name}: Message: {$error->message}");
        }

        return $this->parseTransactionId((object) $response->result);
    }

    /**
     * @param object $result
     * @return string
     * @throws PaymentServiceException
     */
    private function parseTransactionId(object $result): string
    {
        if (!isset($result->purchase_units)) {
            throw new PaymentServiceException("Response does not contain expected purchase_units field");
        }

        if (count($result->purchase_units) !== 1) {
            throw new PaymentServiceException("Purchase units count is not the expected 1");
        }

        if (!isset($result->purchase_units[0]->payments)) {
            throw new PaymentServiceException("Response does not contain expected payments field");
        }

        if (!isset($result->purchase_units[0]->payments->captures)) {
            throw new PaymentServiceException("Response does not contain expected captures field");
        }

        if (count($result->purchase_units[0]->payments->captures) !== 1) {
            throw new PaymentServiceException("Captures count is not the expected 1");
        }

        if (!isset($result->purchase_units[0]->payments->captures[0]->id)) {
            throw new PaymentServiceException("Response does not contain expected captures[0]->id field");
        }

        return $result->purchase_units[0]->payments->captures[0]->id;
    }
}
