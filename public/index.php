<?php

if (!file_exists('css')) {
    symlink('../vendor/vertexvaar/bluesprints/resources/public/css/', __DIR__ . '/css');
}

putenv('VXVR_BS_ROOT=' . dirname(__DIR__) . DIRECTORY_SEPARATOR);
require('../vendor/autoload.php');
require('../vendor/vertexvaar/bluesprints/resources/public/index.php');
