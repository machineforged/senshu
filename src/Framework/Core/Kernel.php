<?php
declare(strict_types = 1);

namespace LapisAngularis\Senshu\Framework\Core;

use LapisAngularis\Senshu\Framework\DependencyInjection\CoreDependencyManager;

class Kernel
{
    const NAME = 'OphagaCore';
    const VERSION = '0.0.8';
    const RELEASE_VERSION = 0;
    const FEATURE_VERSION = 0;
    const PATCH_VERSION = 8;
    const VERSION_CODENAME = 'alpha';
    const VERSION_ID = 8;

    protected $env = 'prod';
    protected $dependencyManager;

    public function __construct(string $env)
    {
        $this->env = $env;
    }

    public function getCoreName(): string
    {
        return self::NAME;
    }

    public function getVersion(): string
    {
        return self::VERSION . '-' . self::VERSION_CODENAME;
    }

    public function getVersionId(): int
    {
        return self::VERSION_ID;
    }

    public function getEnvironment(): string
    {
        return $this->env;
    }

    public function getReleaseInfo(): string
    {
        return (string) $this->getCoreName() . ' '
            . $this->getVersion() . ' '
            . $this->getEnvironment() . ', version id: '
            . $this->getVersionId()
        ;
    }

    public function isDevMode(): bool
    {
        return $this->getEnvironment() === 'dev' ? true : false;
    }

    protected function initializeDependencyManager(): void
    {
        $this->dependencyManager = new CoreDependencyManager();
    }

    protected function initializeContainers(): void
    {
        $this->initializeKernelContainer();
        $this->isDevMode() ? $this->dependencyManager->bootDevServices() : $this->dependencyManager->bootServices();
    }

    protected function bootConfig(): void
    {
        $this->dependencyManager->bootMainConfig();
        $container = $this->dependencyManager->getContainer('ophagacore.config.main');
        $this->isDevMode() ? $container->createDevConfig() : $container->createConfig();
    }

    protected function initializeKernelContainer(): void
    {
        $this->dependencyManager->setContainer('ophagacore.kernel', $this);
    }

    protected function handleDevErrors(): void
    {
        $errorHandler = $this->dependencyManager->getContainer('ophagacore.error.whoops');
        $prettyPageHandler = $this->dependencyManager->getContainer('ophagacore.error.prettypage');

        if ($this->env !== 'prod') {
            $errorHandler->pushHandler($prettyPageHandler);
        } else {
            $errorHandler->pushHandler(function($e){
                die ('omg you broke the internet :/');
            });
        }

        $errorHandler->register();
    }

    protected function createRoutes(): void
    {
        $this->dependencyManager->getContainer('ophagacore.config.routes')->createRoutes();
    }

    protected function handle(): void
    {
        $this->dependencyManager->getContainer('ophagacore.route.router')->matchRequest();
    }

    public function initialize(): void
    {
        $this->initializeDependencyManager();
        $this->bootConfig();
        $this->initializeContainers();
        $this->handleDevErrors();
        $this->createRoutes();
        $this->handle();
    }
}
