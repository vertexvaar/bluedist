<?php
namespace VerteXVaaR\BlueFluid\Mvc;

use TYPO3Fluid\Fluid\View\TemplateView;
use VerteXVaaR\BlueSprints\Http\Response;
use VerteXVaaR\BlueSprints\Mvc\TemplateRendererInterface;

/**
 * Class FluidAdapter
 */
class FluidAdapter implements TemplateRendererInterface
{
    /**
     * @var Response
     */
    protected $response = null;

    /**
     * @var array
     */
    protected $routeConfiguration = [];

    /**
     * @var TemplateView
     */
    protected $view = null;

    public function __construct(Response $response)
    {
        $this->response = $response;
        $this->view = new TemplateView();
    }

    public function render($templateName = '')
    {
        $controllerPath = strtr($this->routeConfiguration['controller'], '\\', '/');
        $this->view->getTemplatePaths()->setTemplateRootPaths(
            [VXVR_BS_ROOT . 'view/Template/' . $controllerPath . '/']
        );
        $this->view->getTemplatePaths()->setLayoutRootPaths([VXVR_BS_ROOT . 'view/Layout/' . $controllerPath . '/']);
        $this->view->getTemplatePaths()->setPartialRootPaths([VXVR_BS_ROOT . 'view/Partial/' . $controllerPath . '/']);
        $this->view->getRenderingContext()->setControllerName(
            substr($controllerPath, strrpos($controllerPath, '/') + 1)
        );
        if (empty($templateName)) {
            $templateName = $this->routeConfiguration['action'];
        }
        $this->response->appendContent($this->view->render($templateName));
    }

    /**
     * @param string $key
     * @param null $value
     * @return void
     */
    public function setVariable($key = '', $value = null)
    {
        $this->view->assign($key, $value);
    }

    /**
     * @param array $routeConfiguration
     */
    public function setRouteConfiguration(array $routeConfiguration)
    {
        $this->routeConfiguration = $routeConfiguration;
    }
}
