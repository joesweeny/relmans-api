<?php

namespace Relmans\Bootstrap;

class ConfigFactory
{
    public static function create(array $overrides = []): Config
    {
        return new Config(array_merge([
            'database' => [
                'default' => [
                    'name' => getenv('DB_NAME'),
                    'user' => getenv('DB_USER'),
                    'password' => getenv('DB_PASSWORD'),
                    'host' => getenv('DB_HOST'),
                    'driver' => getenv('DB_DRIVER'),
                ],
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
        ], $overrides));
    }
}
