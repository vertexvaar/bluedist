<?php

if (!file_exists('css')) {
    symlink('../vendor/vertexvaar/bluesprints/res/public/css/', __DIR__ . '/css');
}

putenv('VXVR_BS_ROOT=' . dirname(__DIR__) . DIRECTORY_SEPARATOR);
require('../vendor/autoload.php');
require('../vendor/vertexvaar/bluesprints/res/public/index.php');
