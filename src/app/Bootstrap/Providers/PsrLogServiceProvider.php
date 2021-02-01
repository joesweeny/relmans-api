<?php

namespace Relmans\Bootstrap\Providers;

use DI\Definition\Helper\DefinitionHelper;
use Relmans\Bootstrap\Config;
use Relmans\Bootstrap\ServiceProvider;
use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Handler\NullHandler;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Sentry\Monolog\Handler;
use Sentry\SentrySdk;

class PsrLogServiceProvider implements ServiceProvider
{
    /**
     * @return array|DefinitionHelper[]
     */
    public function getDefinitions(): array
    {
        return [
            Handler::class => \DI\factory(function (ContainerInterface $container) {
                $config = $container->get(Config::class);

                $options = [
                    'dsn' => $config->get('log.sentry.dsn'),
                ];


                \Sentry\init($options);

                return new Handler(SentrySdk::getCurrentHub(), $config->get('log.sentry.level'));
            }),

            LoggerInterface::class => \DI\factory(function (ContainerInterface $container) {
                $config = $container->get(Config::class);

                switch ($config->get('log.logger')) {
                    case 'monolog':
                        $logger = new Logger('error');
                        $logger->pushHandler($stdout = new ErrorLogHandler());
                        $stdout->setFormatter(new JsonFormatter());

                        if ($config->get('log.sentry.enabled')) {
                            $logger->pushHandler($container->get(Handler::class));
                        }

                        return $logger;
                    case 'null':
                        return new NullHandler();
                    default:
                        throw new \UnexpectedValueException("Logger '{$config->get('log.logger')}' not supported");
                }
            })
        ];
    }
}
