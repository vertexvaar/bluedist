<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueContainer\Composer\Steps;

use Symfony\Component\DependencyInjection\ContainerBuilder;

interface Step
{
    public function run(ContainerBuilder $container): void;
}
