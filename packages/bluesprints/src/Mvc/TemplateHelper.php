<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints\Mvc;

use VerteXVaaR\BlueSprints\Utility\Files;

/**
 * Class TemplateHelper
 */
class TemplateHelper
{
    /**
     * @var string
     */
    protected $fileName = '';

    /**
     * @var mixed[]
     */
    protected $variables = [];

    /**
     * @param string $fileName
     * @param array $variables
     */
    public function requireLayout($fileName = '', array $variables = [])
    {
        $this->fileName = $fileName;
        $this->variables = $variables;
    }

    /**
     * @param string $body
     * @return string
     */
    public function renderLayoutContent(string $body = ''): string
    {
        if (!empty($this->fileName)) {
            ob_start();
            $this->variables['body'] = $body;
            Files::requireFile('app/view/Layout/' . $this->fileName . '.php', $this->variables);
            $content = ob_get_contents();
            ob_end_clean();
            return $content;
        }
        return $body;
    }
}
