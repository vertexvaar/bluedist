<?php

namespace VerteXVaaR\BlueDistTest\Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

use Codeception\Module;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Dotenv\Dotenv;
use VerteXVaaR\BlueContainer\Generated\DI;
use VerteXVaaR\BlueWeb\Application;

use function CoStack\Lib\concat_paths;
use function getenv;

class Functional extends Module
{
    private ?ResponseInterface $response = null;

    public function amOnPage(string $path): void
    {
        $dotEnvFile = concat_paths(getenv('VXVR_BS_TEST_ROOT'), '.env');
        if (file_exists($dotEnvFile)) {
            $dotenv = new Dotenv();
            $dotenv->usePutenv();
            $dotenv->loadEnv($dotEnvFile, null, 'dev', [], true);
        }

        if (empty(ini_get('date.timezone'))) {
            date_default_timezone_set('UTC');
        }

        $request = new ServerRequest('GET', $path);

        $di = new DI();
        $di->set(ServerRequestInterface::class, $request);

        $application = $di->get(Application::class);
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
