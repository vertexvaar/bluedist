<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueDebug\Collector;

use Psr\Http\Message\ServerRequestInterface;
use VerteXVaaR\BlueDebug\CollectorRendering;

class RequestCollector implements Collector
{
    private ?ServerRequestInterface $request = null;

    public function collect(ServerRequestInterface $request): void
    {
        $this->request = $request;
    }

    public function getRequest(): ?ServerRequestInterface
    {
        return $this->request;
    }

    public function render(): CollectorRendering
    {
        return new CollectorRendering(
            'Request',
            $this->request ? (string)$this->request->getUri() : 'none',
        );
    }
}
