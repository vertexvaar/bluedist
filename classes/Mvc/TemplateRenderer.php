<?php
declare(strict_types=1);
namespace VerteXVaaR\BlueSprints\Mvc;

use VerteXVaaR\BlueSprints\Http\Response;
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
     * @var Response
     */
    protected $response = null;

    /**
     * @var array
     */
    protected $routeConfiguration = [];

    /**
     * @param Response $response
     * @return TemplateRenderer
     */
    public function __construct(Response $response)
    {
        $this->response = $response;
        $this->templateHelper = new TemplateHelper();
    }

    /**
     * @param string $templateName
     * @return void
     */
    public function render($templateName = '')
    {
        if ($templateName === '') {
            $templateName = $this->getDefaultTemplateName();
        }
        $this->setVariable('templateHelper', $this->templateHelper);
        ob_start();
        Files::requireFile('view/Template/' . $templateName . '.php', $this->variables);
        $body = ob_get_contents();
        $this->response->appendContent($this->templateHelper->renderLayoutContent($body));
        ob_end_clean();
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function setVariable($key = '', $value = null)
    {
        $this->variables[$key] = $value;
    }

    /**
     * @return string
     */
    protected function getDefaultTemplateName()
    {
        $templateName = ucfirst($this->routeConfiguration['action']);
        $templatePath = Folders::classNameToFolderName($this->routeConfiguration['controller']);
        return $templatePath . $templateName;
    }

    /**
     * @param array $routeConfiguration
     */
    public function setRouteConfiguration(array $routeConfiguration)
    {
        $this->routeConfiguration = $routeConfiguration;
    }
}
