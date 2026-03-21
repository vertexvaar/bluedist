<?php

use CoStack\DependencyInjectionAdditions\InjectionAware\InjectionAwareCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

return static function (ContainerBuilder $container): void {
    InjectionAwareCompilerPass::register($container);
};
