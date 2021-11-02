<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints\Mvc;

use VerteXVaaR\BlueSprints\Utility\Files;

class TemplateHelper
{
    protected string $fileName = '';

    /** @var mixed[] */
    protected array $variables = [];

    public function requireLayout(string $fileName = '', array $variables = []): void
    {
        $this->fileName = $fileName;
        $this->variables = $variables;
    }

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
