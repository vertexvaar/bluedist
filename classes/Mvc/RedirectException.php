<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints\Mvc;

use Exception;

class RedirectException extends Exception
{
    public const MOVED_PERMANENTLY = 301;
    public const FOUND = 302;
    public const SEE_OTHER = 303;
    public const TEMPORARY_REDIRECT = 307;
    public const PERMANENT_REDIRECT = 308;

    /** @var string */
    protected $url;

    /** @var int */
    protected $status;

    public static function forUrl(string $url, int $status = self::SEE_OTHER)
    {
        $self = new RedirectException('Redirect', 1589410111);
        $self->url = $url;
        $self->status = $status;
        return $self;
    }

    public function getUrl(): string { return $this->url; }

    public function getStatus(): int { return $this->status; }
}
