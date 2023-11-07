<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueDebug\Collector;

use Psr\Http\Message\ServerRequestInterface;
use VerteXVaaR\BlueDebug\Rendering\CollectorRendering;

use function array_diff_assoc;
use function array_filter;
use function array_keys;
use function array_shift;
use function end;

class RequestCollector implements Collector
{
    /** @var array<ServerRequestInterface> */
    private array $requests = [];

    public function collect(ServerRequestInterface $request, string $middleware): void
    {
        $this->requests[$middleware] = $request;
    }

    public function getLastRequest(): ?ServerRequestInterface
    {
        return end($this->requests) ?: null;
    }

    public function render(): CollectorRendering
    {
        $finalRequest = end($this->requests);
        $requests = $this->requests;
        $previousRequest = array_shift($requests);
        $diffs = [];
        foreach ($requests as $middleware => $request) {
            $diffs[$middleware] = $this->diffRequest($previousRequest, $request);
            $previousRequest = $request;
        }
        $diffs = array_filter($diffs);

        $table = $diffs;
        return new CollectorRendering(
            'Request',
            $finalRequest ? (string)$finalRequest->getUri() : 'none',
            $table,
        );
    }

    protected function diffRequest(ServerRequestInterface $previousRequest, ServerRequestInterface $request)
    {
        $previousHeaders = [];
        foreach (array_keys($previousRequest->getHeaders()) as $header) {
            $previousHeaders[$header] = $previousRequest->getHeaderLine($header);
        }
        $newHeaders = [];
        foreach (array_keys($request->getHeaders()) as $header) {
            $newHeaders[$header] = $request->getHeaderLine($header);
        }
        $diff = [];
        $diff['removedHeader'] = array_diff_assoc($previousHeaders, $newHeaders);
        $diff['addedHeader'] = array_diff_assoc($newHeaders, $previousHeaders);

        $previousAttributes = $previousRequest->getAttributes();
        $newAttributes = $request->getAttributes();
        foreach ($previousAttributes as $key => $value) {
            if (!isset($newAttributes[$key])) {
                $diff['removedAttribute'][$key] = $value;
            } elseif ($newAttributes[$key] !== $value) {
                $diff['changedAttribute'][$key] = $value;
            }
        }
        foreach ($newAttributes as $key => $value) {
            if (!isset($previousAttributes[$key])) {
                $diff['addedAttribute'][$key] = $value;
            }
        }

        return array_filter($diff);
    }
}
