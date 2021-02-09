<?php

namespace Relmans\Bootstrap;

use DI\ContainerBuilder;
use DI\Definition\Helper\DefinitionHelper;
use Relmans\Application\Http\Middleware\CorsMiddleware;
use Relmans\Bootstrap\Providers\CommandBusServiceProvider;
use Relmans\Bootstrap\Providers\DoctrineServiceProvider;
use Relmans\Bootstrap\Providers\PsrLogServiceProvider;
use Relmans\Bootstrap\Providers\ReaderServiceProvider;
use Relmans\Bootstrap\Providers\RepositoryServiceProvider;
use Relmans\Bootstrap\Providers\WriterServiceProvider;
use Relmans\Framework\Error\JsendErrorHandler;
use Relmans\Framework\Routing\RouteMapper;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Relmans\Framework\Time\Clock;
use Relmans\Framework\Time\SystemClock;
use Slim\App;
use Slim\Factory\AppFactory;

class ContainerFactory
{
    /**
     * @var array|ServiceProvider[]
     */
    private const PROVIDERS = [
        CommandBusServiceProvider::class,
        DoctrineServiceProvider::class,
        PsrLogServiceProvider::class,
        ReaderServiceProvider::class,
        RepositoryServiceProvider::class,
        WriterServiceProvider::class,
    ];

    /**
     * @param Config|null $config
     * @return ContainerInterface
     * @throws \Exception
     */
    public function create(?Config $config = null): ContainerInterface
    {
        return (new ContainerBuilder())
            ->useAutowiring(true)
            ->useAnnotations(false)
            ->ignorePhpDocErrors(true)
            ->addDefinitions($this->buildDefinitions($config))
            ->build();
    }

    private function buildDefinitions(?Config $config): array
    {
        return array_merge(
            $this->defineConfig($config),
            $this->defineFramework(),
            $this->getProviderDefinitions(),
        );
    }

    /**
     * @return array|DefinitionHelper[]
     */
    protected function getProviderDefinitions(): array
    {
        return array_merge(...array_map(static function (ServiceProvider $provider) {
            return $provider->getDefinitions();
        }, $this->getProviders()));
    }

    /**
     * @return ServiceProvider[]
     */
    protected function getProviders(): array
    {
        return array_map(static function (string $provider) {
            return new $provider();
        }, self::PROVIDERS);
    }

    private function defineConfig(?Config $config): array
    {
        return [
            Config::class => \DI\factory(function () use ($config) {
                return $config;
            })
        ];
    }

    private function defineFramework(): array
    {
        return [
            App::class => \DI\factory(function (ContainerInterface $container) {
                AppFactory::setContainer($container);

                $app = AppFactory::create();

                $container->get(RouteMapper::class)->map($app);

                $app->addRoutingMiddleware();

                $errorMiddleware = $app->addErrorMiddleware(
                    true,
                    true,
                    true,
                    $container->get(LoggerInterface::class)
                );

                $errorMiddleware->setDefaultErrorHandler(new JsendErrorHandler());

                $app->add($container->get(CorsMiddleware::class));

                return $app;
            }),

            Clock::class => function () {
                return new SystemClock();
            }
        ];
    }
}
