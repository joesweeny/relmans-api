<?php

namespace Relmans\Bootstrap;

class ConfigFactory
{
    public static function create(array $overrides = []): Config
    {
        return new Config(array_merge([
            'aws' => [
                'key' => getenv('AWS_KEY'),
                'secret' => getenv('AWS_SECRET'),
                'region' => 'eu-west-2',
            ],

            'cors' => [
                'allowed-origins' => [
                    'http://localhost:3000',
                    'https://admin.relmans.co.uk',
                    'https://shop.relmans.co.uk',
                ],
            ],

            'database' => [
                'default' => [
                    'name' => getenv('DB_NAME'),
                    'user' => getenv('DB_USER'),
                    'password' => getenv('DB_PASSWORD'),
                    'host' => getenv('DB_HOST'),
                    'driver' => getenv('DB_DRIVER'),
                ],
            ],

            'email' => [
                'driver' => getenv('EMAIL_DRIVER') ?: 'log',
            ],

            'log' => [
                /**
                 * Which psr/log implementation to use. Options: monolog, null
                 */
                'logger' => getenv('LOG_LOGGER') ?: 'monolog',

                'sentry' => [
                    'enabled' => in_array(getenv('LOG_SENTRY_ENABLED'), ['true', true], false) ?: false,

                    'dsn' => getenv('SENTRY_DSN'),

                    'level' => getenv('SENTRY_LOG_LEVEL') ?: \Monolog\Logger::DEBUG,
                ],
            ],

            'payment' => [
                'driver' => getenv('PAYMENT_DRIVER') ?: 'log',
            ],

            'paypal' => [
                'environment' => getenv('PAYMENT_ENVIRONMENT'),
                'client_id' => getenv('PAYPAL_CLIENT_ID'),
                'secret' => getenv('PAYPAL_SECRET'),
            ]
        ], $overrides));
    }
}
