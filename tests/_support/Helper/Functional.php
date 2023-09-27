<?php

namespace VerteXVaaR\BlueDistTest\Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Message\ResponseInterface;
use VerteXVaaR\BlueSprints\Http\Application;

class Functional extends \Codeception\Module
{
    private ?ResponseInterface $response = null;

    public function amOnPage(string $path): void
    {
        $request = new ServerRequest('GET', $path);

        $application = new Application();
        $this->response = $application->run($request);
    }

    public function see(string $content)
    {
        $stream = $this->response->getBody();
        $stream->rewind();
        $body = $stream->getContents();
        $this->assertStringContainsString($content, $body);
    }
}
