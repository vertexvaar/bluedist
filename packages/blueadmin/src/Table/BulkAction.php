<?php

namespace VerteXVaaR\BlueAdmin\Table;

use Closure;
use VerteXVaaR\BlueAdmin\Exception\UnsupportedBulkActionMethodException;
use VerteXVaaR\BlueSprints\Mvcr\Model\Entity;
use VerteXVaaR\BlueWeb\Enum\HttpMethod;

use function array_key_exists;
use function CoStack\Lib\evaluate;

readonly class BulkAction
{
    /**
     * @var array<string|Closure>
     */
    protected array $attributes;

    /**
     * @param string|Closure $title
     * @param string|Closure $link
     * @param HttpMethod $method
     * @param array<string|Closure> $attributes
     *
     * @throws UnsupportedBulkActionMethodException
     */
    public function __construct(
        protected string|Closure $title,
        protected string|Closure $link,
        public HttpMethod $method = HttpMethod::GET,
        array $attributes = [],
    ) {
        if (HttpMethod::GET === $this->method) {
            $attributes['class'] ??= 'button is-small is-info';
        } elseif (HttpMethod::POST === $this->method) {
            $attributes['class'] ??= 'button is-small is-danger';
            $attributes['style'] ??= 'display:inline';
        } else {
            throw new UnsupportedBulkActionMethodException($this->method->name);
        }
        $this->attributes = $attributes;
    }

    public function getTitle(Entity $entity): string
    {
        return evaluate($this->title, $entity);
    }

    public function getLink(Entity $entity): string
    {
        return evaluate($this->link, $entity);
    }

    public function getAttribute(string $name, Entity $entity): ?string
    {
        if (!array_key_exists($name, $this->attributes)) {
            return null;
        }
        $attribute = $this->attributes[$name];

        return evaluate($attribute, $entity);
    }
}
