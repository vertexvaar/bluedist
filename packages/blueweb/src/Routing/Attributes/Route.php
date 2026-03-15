<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueWeb\Routing\Attributes;

use Attribute;
use VerteXVaaR\BlueWeb\Enum\HttpMethod;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
readonly class Route
{
    public const HttpMethod GET = HttpMethod::GET;
    public const HttpMethod HEAD = HttpMethod::HEAD;
    public const HttpMethod POST = HttpMethod::POST;
    public const HttpMethod PUT = HttpMethod::PUT;
    public const HttpMethod DELETE = HttpMethod::DELETE;
    public const HttpMethod CONNECT = HttpMethod::CONNECT;
    public const HttpMethod OPTIONS = HttpMethod::OPTIONS;
    public const HttpMethod TRACE = HttpMethod::TRACE;

    public function __construct(
        public string $path,
        public HttpMethod $method = HttpMethod::GET,
        public int $priority = 100,
    ) {}
}
