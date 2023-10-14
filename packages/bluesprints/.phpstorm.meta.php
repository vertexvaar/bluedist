<?php

namespace PHPSTORM_META {

    override(\Symfony\Component\DependencyInjection\Container::get(), type(0));
    override(\VerteXVaaR\BlueSprints\Mvc\Repository::findByUuid(), type(0));
    override(\VerteXVaaR\BlueSprints\Mvc\Repository::findAll(), map(['' => '@[]']));
}
