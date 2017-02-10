<?php
namespace VerteXVaaR\BlueFluid\Mvc;

use TYPO3Fluid\Fluid\View\TemplateView;
use VerteXVaaR\BlueSprints\Mvc\TemplateRendererInterface;

/**
 * Class FluidAdapter
 */
class FluidAdapter implements TemplateRendererInterface
{
    /**
     * @var array
     */
    protected $routeConfiguration = [];

    /**
     * @var TemplateView
     */
    protected $view = null;

    /**
     * FluidAdapter constructor.
     */
    public function __construct()
    {
        $this->view = new TemplateView();
    }

    /**
     * @param string $templateName
     * @return string
     */
    public function render(string $templateName = ''): string
    {
        $controller = strtr($this->routeConfiguration['controller'], '\\', '/');
        $viewRootPath = VXVR_BS_ROOT . 'app/view/';

        $this->view->getTemplatePaths()->setTemplateRootPaths([$viewRootPath . 'Template/' . $controller . '/']);
        $this->view->getTemplatePaths()->setLayoutRootPaths([$viewRootPath . 'Layout/' . $controller . '/']);
        $this->view->getTemplatePaths()->setPartialRootPaths([$viewRootPath . 'Partial/' . $controller . '/']);
        $this->view->getRenderingContext()->setControllerName(substr($controller, strrpos($controller, '/') + 1));

        if (empty($templateName)) {
            $templateName = $this->routeConfiguration['action'];
        }
        return $this->view->render($templateName);
    }

    /**
     * @param string $key
     * @param null $value
     * @return void
     */
    public function setVariable(string $key, $value = null)
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
