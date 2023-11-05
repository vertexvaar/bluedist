<?php

use Symfony\Component\DependencyInjection\ContainerBuilder;
use VerteXVaaR\BlueDebug\Collector\Collector;
use VerteXVaaR\BlueDebug\DependencyInjection\CollectorCompilerPass;

return static function (ContainerBuilder $container): void {
    $container->registerForAutoconfiguration(Collector::class)->addTag('vertexvaar.bluedebug.debug.collector');
    $container->addCompilerPass(new CollectorCompilerPass('vertexvaar.bluedebug.debug.collector'));
};
