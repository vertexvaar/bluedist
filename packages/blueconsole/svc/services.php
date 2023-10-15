<?php

use Symfony\Component\DependencyInjection\ContainerBuilder;
use VerteXVaaR\BlueConsole\DependencyInjection\CommandCollectorCompilerPass;

return static function (ContainerBuilder $container): void {
    $container->addCompilerPass(new CommandCollectorCompilerPass());
};
