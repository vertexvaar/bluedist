<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints\Mvc;

/**
 * Interface TemplateRendererInterface
 */
interface TemplateRendererInterface
{
    /**
     * TemplateRendererInterface constructor.
     */
    public function __construct();

    /**
     * @param string $templateName
     * @return string
     */
    public function render(string $templateName = ''): string;

    /**
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function setVariable(string $key, $value);

    /**
     * @param array $routeConfiguration
     * @return void
     */
    public function setRouteConfiguration(array $routeConfiguration);
}
