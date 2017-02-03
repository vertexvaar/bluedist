<?php
namespace VerteXVaaR\BlueSprints\Mvc;

use VerteXVaaR\BlueSprints\Http\Request;
use VerteXVaaR\BlueSprints\Http\Response;

/**
 * Class AbstractController
 *
 * @package VerteXVaaR\BlueSprints\Mvc
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
     * @return AbstractController
     */
    final public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
        $this->templateRenderer = new TemplateRenderer($this->response);
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
     * @return void
     */
    public function callActionMethod(array $configuration)
    {
        $this->templateRenderer->setRouteConfiguration($configuration);
        $this->{$configuration['action']}();
        if (true === $this->renderTemplate) {
            $this->templateRenderer->render();
        }
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
