<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueContainer\Composer\Steps;

use Symfony\Component\DependencyInjection\ContainerBuilder;

use function file_exists;

class RequireLocalAutoloader implements Step
{
    public function run(ContainerBuilder $container): void
    {
        $composer = $container->get('composer');
        $autoloadFile = $composer->getConfig()->get('vendor-dir') . '/autoload.php';
        if (!file_exists($autoloadFile)) {
            $io = $container->get('io');
            $io->writeError('Autoload not yet dumped');
            return;
        }
        require $autoloadFile;
    }
}
