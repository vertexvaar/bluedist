<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueFluid\Mvc;

use TYPO3Fluid\Fluid\View\TemplateView;
use VerteXVaaR\BlueSprints\Environment\Paths;
use VerteXVaaR\BlueSprints\Mvc\TemplateRenderer;

use function CoStack\Lib\concat_paths;
use function getenv;
use function str_contains;
use function strrpos;
use function substr;

use const DIRECTORY_SEPARATOR;

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
        $viewRootPath = concat_paths(getenv('VXVR_BS_ROOT'), $this->paths->view);

        $this->view->getTemplatePaths()->setTemplateRootPaths(
            [concat_paths($viewRootPath, 'Template', $controller, DIRECTORY_SEPARATOR)]
        );
        $this->view->getTemplatePaths()->setLayoutRootPaths(
            [concat_paths($viewRootPath, 'Layout', $controller, DIRECTORY_SEPARATOR)]
        );
        $this->view->getTemplatePaths()->setPartialRootPaths(
            [concat_paths($viewRootPath, 'Partial', $controller, DIRECTORY_SEPARATOR)]
        );
        if (str_contains($controller, '/')) {
            $this->view->getRenderingContext()->setControllerName(substr($controller, strrpos($controller, '/') + 1));
        } else {
            $this->view->getRenderingContext()->setControllerName($controller);
        }

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
