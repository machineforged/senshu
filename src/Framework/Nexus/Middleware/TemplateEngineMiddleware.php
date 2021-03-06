<?php
declare(strict_types = 1);

namespace LapisAngularis\Senshu\Framework\Nexus\Middleware;

use LapisAngularis\Senshu\Framework\Config\MainConfigInterface;
use LapisAngularis\Senshu\Framework\Nexus\MiddlewareInterface;
use LapisAngularis\Senshu\Framework\Nexus\UtilsInterface;
use LapisAngularis\Senshu\Framework\Service\ServiceInterface;

class TemplateEngineMiddleware implements MiddlewareInterface
{
    protected $config;
    protected $templateUtils;
    protected $templateComposite;

    public function __construct(
        MainConfigInterface $config,
        UtilsInterface $templateUtils,
        ServiceInterface $templateComposite)
    {
        $this->config = $config->getConfigs();
        $this->templateUtils = $templateUtils;
        $this->templateComposite = $templateComposite;
    }

    public function render(array $arguments): string
    {
        $engine = $this->templateComposite->getEngine($this->config['template.engine']);
        $arguments['variables']['middleware'] = $this->templateUtils;
        return $engine->render($arguments['template'], $arguments['variables']);
    }
}
