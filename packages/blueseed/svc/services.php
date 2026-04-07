<?php

use Symfony\Component\DependencyInjection\ContainerBuilder;
use VerteXVaaR\BlueSeed\Seeder\Seeder;

return static function (ContainerBuilder $container): void {
    $container->registerForAutoconfiguration(Seeder::class)->addTag('app.seeder');
};
