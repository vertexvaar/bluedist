<?php

namespace PHPSTORM_META {

    use Psr\Http\Message\ResponseInterface;
    use Psr\Http\Message\ServerRequestInterface;
    use VerteXVaaR\BlueAuth\Mvcr\Model\Session;
    use VerteXVaaR\BlueDebug\Service\DebugCollector;
    use VerteXVaaR\BlueSprints\Routing\Route;

    override(
        DebugCollector::getItem(),
        map([
            'request' => ServerRequestInterface::class,
            'response' => ResponseInterface::class,
        ])
    );
}
