<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints\Mvc;

use VerteXVaaR\BlueSprints\Utility\Files;
use VerteXVaaR\BlueSprints\Utility\Folders;

class TemplateRenderer implements TemplateRendererInterface
{
    protected TemplateHelper $templateHelper;

    protected array $variables = [];

    protected array $routeConfiguration = [];

    public function __construct()
    {
        $this->templateHelper = new TemplateHelper();
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function setVariable(string $key, $value): void
    {
        $this->variables[$key] = $value;
    }

    /**
     * @param array $routeConfiguration
     */
    public function setRouteConfiguration(array $routeConfiguration): void
    {
        $this->routeConfiguration = $routeConfiguration;
    }

    public function render(string $templateName = ''): string
    {
        if ($templateName === '') {
            $templateName = $this->getDefaultTemplateName();
        }
        $this->setVariable('templateHelper', $this->templateHelper);
        ob_start();
        Files::requireFile('app/view/Template/' . $templateName . '.php', $this->variables);
        $body = ob_get_contents();
        $content = $this->templateHelper->renderLayoutContent($body);
        ob_end_clean();
        return $content;
    }

    protected function getDefaultTemplateName(): string
    {
        $templateName = ucfirst($this->routeConfiguration['action']);
        $templatePath = Folders::classNameToFolderName($this->routeConfiguration['controller']);
        return $templatePath . $templateName;
    }
}
