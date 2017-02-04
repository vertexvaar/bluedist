<?php
declare(strict_types=1);
namespace VerteXVaaR\BlueSprints\Mvc;

use VerteXVaaR\BlueSprints\Http\Response;

/**
 * Interface TemplateRendererInterface
 */
interface TemplateRendererInterface
{
    /**
     * TemplateRendererInterface constructor.
     * @param Response $response
     */
    public function __construct(Response $response);

    /**
     * @param string $templateName
     * @return mixed
     */
    public function render($templateName = '');

    /**
     * @param string $key
     * @param null $value
     * @return mixed
     */
    public function setVariable($key = '', $value = null);

    /**
     * @param array $routeConfiguration
     * @return void
     */
    public function setRouteConfiguration(array $routeConfiguration);
}
