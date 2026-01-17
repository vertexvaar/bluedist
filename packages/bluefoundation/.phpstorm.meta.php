<?php

namespace PHPSTORM_META {

    override(
        \Symfony\Component\DependencyInjection\ContainerBuilder::get(),
        map([
            '_input' => \Symfony\Component\Console\Input\InputInterface::class,
            '_output' => \Symfony\Component\Console\Output\OutputInterface::class,
            '' => '@',
        ]),
    );
}
