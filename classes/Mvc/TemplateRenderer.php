<?php
declare(strict_types=1);
namespace VerteXVaaR\BlueSprints\Mvc;

use VerteXVaaR\BlueSprints\Utility\Files;
use VerteXVaaR\BlueSprints\Utility\Folders;

/**
 * Class TemplateRenderer
 */
class TemplateRenderer implements TemplateRendererInterface
{
    /**
     * @var array
     */
    protected $variables = [];

    /**
     * @var TemplateHelper
     */
    protected $templateHelper = null;

    /**
     * @var array
     */
    protected $routeConfiguration = [];

    /**
     * TemplateRenderer constructor.
     */
    public function __construct()
    {
        $this->templateHelper = new TemplateHelper();
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function setVariable(string $key, $value = null)
    {
        $this->variables[$key] = $value;
    }

    /**
     * @param array $routeConfiguration
     */
    public function setRouteConfiguration(array $routeConfiguration)
    {
        $this->routeConfiguration = $routeConfiguration;
    }

    /**
     * @param string $templateName
     * @return string
     */
    public function render(string $templateName = ''): string
    {
        if ($templateName === '') {
            $templateName = $this->getDefaultTemplateName();
        }
        $this->setVariable('templateHelper', $this->templateHelper);
        ob_start();
        Files::requireFile('view/Template/' . $templateName . '.php', $this->variables);
        $body = ob_get_contents();
        $content = $this->templateHelper->renderLayoutContent($body);
        ob_end_clean();
        return $content;
    }

    /**
     * @return string
     */
    protected function getDefaultTemplateName(): string
    {
        $templateName = ucfirst($this->routeConfiguration['action']);
        $templatePath = Folders::classNameToFolderName($this->routeConfiguration['controller']);
        return $templatePath . $templateName;
    }
}
