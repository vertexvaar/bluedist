<?php

namespace PHPSTORM_META {

    use Psr\Http\Message\ResponseInterface;
    use Psr\Http\Message\ServerRequestInterface;
    use VerteXVaaR\BlueDebug\Service\DebugCollector;

    override(
        DebugCollector::getItem(),
        map([
            'request' => ServerRequestInterface::class,
            'response' => ResponseInterface::class,
        ])
    );
}
