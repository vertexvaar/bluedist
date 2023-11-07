<?php

use Symfony\Component\DependencyInjection\ContainerBuilder;
use VerteXVaaR\BlueConfig\Definition\Definition;
use VerteXVaaR\BlueConfig\Provider\Provider;

return static function (ContainerBuilder $container): void {
    $container->registerForAutoconfiguration(Definition::class)->addTag('vertexvaar.bluesprints.config.definition');
    $container->registerForAutoconfiguration(Provider::class)->addTag('vertexvaar.bluesprints.config.provider');
};
