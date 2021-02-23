<?php

namespace Relmans\Bootstrap\Providers;

use Aws\Ses\SesClient;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Relmans\Bootstrap\Config;
use Relmans\Bootstrap\ServiceProvider;
use Relmans\Framework\Email\AWS\AwsEmailService;
use Relmans\Framework\Email\EmailService;
use Relmans\Framework\Email\Log\LoggerEmailService;

class EmailServiceProvider implements ServiceProvider
{
    public function getDefinitions(): array
    {
        return [
            EmailService::class => \DI\factory(function (ContainerInterface $container) {
                $config = $container->get(Config::class);

                $driver = $config->get('email.driver');

                if ($driver === 'aws') {
                    $client = new SesClient([
                        'credentials' => [
                            'key' => $config->get('aws.key'),
                            'secret' => $config->get('aws.secret'),
                        ],
                        'region' => $config->get('aws.region'),
                        'version' => 'latest'
                    ]);

                    return new AwsEmailService($client);
                }

                if ($driver === 'log') {
                    return new LoggerEmailService($container->get(LoggerInterface::class));
                }

                throw new \RuntimeException("Email driver {$driver} is not supported");
            })
        ];
    }
}
