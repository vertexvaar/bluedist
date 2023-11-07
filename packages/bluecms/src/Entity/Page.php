<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueCms\Entity;

use VerteXVaaR\BlueSprints\Mvcr\Model\Entity;

use function base64_decode;

class Page extends Entity
{
    public string $title = '';
    public array $contents = [];

    public function getSlug(): string
    {
        return base64_decode($this->identifier);
    }
}
