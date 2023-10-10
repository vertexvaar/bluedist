<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints\Mvc;

interface TemplateRenderer
{
    public function __construct();

    public function render(string $templateName = ''): string;

    /**
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function setVariable(string $key, $value): void;

    public function setRouteConfiguration(array $routeConfiguration): void;
}
