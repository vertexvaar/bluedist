<?php

namespace PHPSTORM_META {

    override(
        \Symfony\Component\DependencyInjection\ContainerBuilder::get(),
        map([
            'io' => \Composer\IO\IOInterface::class,
            'composer' => \Composer\Composer::class,
            'package_iterator' => \VerteXVaaR\BlueContainer\Helper\PackageIterator::class,
            '' => '@',
        ]),
    );
}
