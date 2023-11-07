<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueDebug\Collector;

use Psr\Http\Message\ResponseInterface;
use VerteXVaaR\BlueDebug\Rendering\CollectorRendering;

use function array_diff_assoc;
use function array_filter;
use function array_keys;
use function array_shift;
use function implode;

class ResponseCollector implements Collector
{
    /** @var array<ResponseInterface> */
    private array $responses;

    public function collect(ResponseInterface $response, string $middleware): void
    {
        $this->responses[$middleware] = $response;
    }

    public function render(): CollectorRendering
    {
        $responses = $this->responses;
        $initialResponse = $previousResponse = array_shift($responses);

        $diffs = [];
        foreach ($responses as $middleware => $response) {
            $diffs[$middleware] = $this->diffResponses($previousResponse, $response);
            $previousResponse = $response;
        }
        $diffs = array_filter($diffs);
        $lastResponse = $response ?? $initialResponse;

        $table = $diffs;
        foreach ($lastResponse->getHeaders() as $name => $values) {
            $table['header ' . $name] = implode('; ', $values);
        }
        return new CollectorRendering(
            'Response',
            (string)$lastResponse->getStatusCode(),
            $table,
        );
    }

    protected function diffResponses(ResponseInterface $previousResponse, ResponseInterface $response)
    {
        $previousHeaders = [];
        foreach (array_keys($previousResponse->getHeaders()) as $header) {
            $previousHeaders[$header] = $previousResponse->getHeaderLine($header);
        }
        $newHeaders = [];
        foreach (array_keys($response->getHeaders()) as $header) {
            $newHeaders[$header] = $response->getHeaderLine($header);
        }
        $diff = [];
        $diff['removedHeader'] = array_diff_assoc($previousHeaders, $newHeaders);
        $diff['addedHeader'] = array_diff_assoc($newHeaders, $previousHeaders);
        if ($previousResponse->getStatusCode() !== $response->getStatusCode()) {
            $diff['newStatus'] = $previousResponse->getStatusCode() . '->' . $response->getStatusCode();
        }
        if ($previousResponse->getBody()->getSize() !== $response->getBody()->getSize()) {
            $diff['newSize'] = $previousResponse->getBody()->getSize() . '->' . $response->getBody()->getSize();
        }

        return array_filter($diff);
    }
}
