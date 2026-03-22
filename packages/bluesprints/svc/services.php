<?php

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\DependencyInjection\AddConsoleCommandPass;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;

return static function (ContainerBuilder $container): void {
    $container->registerAttributeForAutoconfiguration(
        AsCommand::class,
        static function (ChildDefinition $definition, AsCommand $attribute): void {
            $definition->addTag('console.command', [
                'command' => $attribute->name,
                'description' => $attribute->description,
                'help' => $attribute->help ?? null,
            ]);
        },
    );
    $container->addCompilerPass(new AddConsoleCommandPass());
};
