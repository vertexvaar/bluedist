<?php

use Symfony\Component\DependencyInjection\ContainerBuilder;
use VerteXVaaR\BlueConfig\Definition\Definition;
use VerteXVaaR\BlueConfig\DependencyInjection\ConfigurationDefinitionCompilerPass;

return static function (ContainerBuilder $container): void {
    $container->registerForAutoconfiguration(Definition::class)->addTag('vertexvaar.bluesprints.config.definition');

    $container->addCompilerPass(new ConfigurationDefinitionCompilerPass('vertexvaar.bluesprints.config.definition'));
};
