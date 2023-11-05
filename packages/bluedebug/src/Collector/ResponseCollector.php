<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueDebug\Collector;

use Psr\Http\Message\ResponseInterface;
use VerteXVaaR\BlueDebug\CollectorRendering;

use function implode;

class ResponseCollector implements Collector
{
    private ResponseInterface $response;

    public function collect(ResponseInterface $response): void
    {
        if (isset($this->response)) {
            return;
        }
        $this->response = $response;
    }

    public function render(): CollectorRendering
    {
        $table = [
            'size' => $this->response->getBody()->getSize(),
        ];
        foreach ($this->response->getHeaders() as $name => $values) {
            $table['header ' . $name] = implode('; ', $values);
        }
        return new CollectorRendering(
            'Response',
            (string)$this->response->getStatusCode(),
            $table,
        );
    }
}
