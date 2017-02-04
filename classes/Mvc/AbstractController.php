<?php
declare(strict_types=1);
namespace VerteXVaaR\BlueSprints\Mvc;

use VerteXVaaR\BlueFluid\Mvc\FluidAdapter;
use VerteXVaaR\BlueSprints\Http\Request;
use VerteXVaaR\BlueSprints\Http\Response;

/**
 * Class AbstractController
 */
abstract class AbstractController
{
    /**
     * @var Request
     */
    protected $request = null;

    /**
     * @var Response
     */
    protected $response = null;

    /**
     * @var TemplateRenderer
     */
    protected $templateRenderer = null;

    /**
     * @var bool Indicates if the template should be rendered after the action has been called
     */
    private $renderTemplate = true;

    /**
     * @param Request $request
     * @param Response $response
     */
    final public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
        if (class_exists(FluidAdapter::class)) {
            $this->templateRenderer = new FluidAdapter();
        } else {
            $this->templateRenderer = new TemplateRenderer();
        }
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param array $configuration
     * @return string
     */
    public function callActionMethod(array $configuration): string
    {
        $this->templateRenderer->setRouteConfiguration($configuration);
        $this->{$configuration['action']}();
        if (true === $this->renderTemplate) {
            return $this->templateRenderer->render();
        }
        return '';
    }

    /**
     * @param string $url
     * @return void
     */
    protected function redirect($url)
    {
        $this->renderTemplate = false;
        $this->response->setHeader('Location', $url);
    }
}
