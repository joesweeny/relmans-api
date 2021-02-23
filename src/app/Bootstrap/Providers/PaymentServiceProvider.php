<?php

namespace Relmans\Bootstrap\Providers;

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
        if ($config->get('paypal.environment') === 'sandbox') {
            $env = new SandboxEnvironment($config->get('paypal.client_id'), $config->get('paypal-secret'));

            return new HttpClient($env);
        }

        if ($config->get('paypal.environment') === 'production') {
            $env = new ProductionEnvironment($config->get('paypal.client_id'), $config->get('paypal-secret'));

            return new HttpClient($env);
        }

        throw new \RuntimeException("Paypal environment {$config->get('paypal.environment')} is not supported");
    }
}
