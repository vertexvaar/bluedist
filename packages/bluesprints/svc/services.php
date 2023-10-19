<?php

use Symfony\Component\DependencyInjection\ContainerBuilder;
use VerteXVaaR\BlueSprints\Environment\DependencyInjection\ConfigCompilerPass;

return static function (ContainerBuilder $container): void {
    $container->addCompilerPass(new ConfigCompilerPass());
};
