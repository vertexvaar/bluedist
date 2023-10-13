<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueFluid\Mvc;

use TYPO3Fluid\Fluid\View\TemplateView;
use VerteXVaaR\BlueSprints\Mvc\TemplateRenderer;
use VerteXVaaR\BlueSprints\Paths;

use const DIRECTORY_SEPARATOR as DS;

class FluidTemplateRenderer implements TemplateRenderer
{
    protected array $routeConfiguration = [];

    protected TemplateView $view;

    public function __construct(private readonly Paths $paths)
    {
        $this->view = new TemplateView();
    }

    public function render(string $templateName = ''): string
    {
        $controller = str_replace('\\', '/', $this->routeConfiguration['controller']);
        $viewRootPath = VXVR_BS_ROOT . DS . $this->paths->view . DS;

        $this->view->getTemplatePaths()->setTemplateRootPaths([$viewRootPath . 'Template/' . $controller . '/']);
        $this->view->getTemplatePaths()->setLayoutRootPaths([$viewRootPath . 'Layout/' . $controller . '/']);
        $this->view->getTemplatePaths()->setPartialRootPaths([$viewRootPath . 'Partial/' . $controller . '/']);
        $this->view->getRenderingContext()->setControllerName(substr($controller, strrpos($controller, '/') + 1));

        if (empty($templateName)) {
            $templateName = $this->routeConfiguration['action'];
        }
        return $this->view->render($templateName);
    }

    public function setVariable(string $key, $value = null): void
    {
        $this->view->assign($key, $value);
    }

    public function setRouteConfiguration(array $routeConfiguration): void
    {
        $this->routeConfiguration = $routeConfiguration;
    }
}
