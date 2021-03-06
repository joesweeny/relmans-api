<?php

namespace Relmans\Bootstrap\Providers;

use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\ProductionEnvironment;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalHttp\HttpClient;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Relmans\Bootstrap\Config;
use Relmans\Bootstrap\ServiceProvider;
use Relmans\Domain\Service\Payment\Log\LoggerPaymentService;
use Relmans\Domain\Service\Payment\PaymentService;
use Relmans\Domain\Service\Payment\PayPal\PayPalPaymentService;

class PaymentServiceProvider implements ServiceProvider
{
    public function getDefinitions(): array
    {
        return [
            PaymentService::class => \DI\factory(function (ContainerInterface $container) {
                $config = $container->get(Config::class);

                $driver = $config->get('payment.driver');

                if ($driver === 'paypal') {
                    return new PayPalPaymentService($this->createPayPalPaymentClient($config));
                }

                if ($driver === 'log') {
                    return new LoggerPaymentService($container->get(LoggerInterface::class));
                }

                throw new \RuntimeException("Payment driver {$driver} is not supported");
            }),
        ];
    }

    private function createPayPalPaymentClient(Config $config): HttpClient
    {
        $environment = $config->get('paypal.environment');
        $clientId = $config->get('paypal.client_id');
        $secret = $config->get('paypal.secret');

        if ($environment === 'sandbox') {
            $env = new SandboxEnvironment($clientId, $secret);

            return new PayPalHttpClient($env);
        }

        if ($environment === 'production') {
            $env = new ProductionEnvironment($clientId, $secret);

            return new PayPalHttpClient($env);
        }

        throw new \RuntimeException("Paypal environment {$config->get('paypal.environment')} is not supported");
    }
}
