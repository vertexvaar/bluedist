<?php

namespace functional;

use VerteXVaaR\BlueDistTest\FunctionalTester;

use function copy;
use function define;
use function dirname;
use function escapeshellarg;
use function exec;
use function file_get_contents;
use function file_put_contents;
use function mkdir;
use function uniqid;

class FirstCest
{
    public function tryLogin(FunctionalTester $I)
    {
        $data = dirname(__DIR__) . '/_data/functional/' . uniqid() . '/';
        mkdir($data, 0777, true);
        define('VXVR_BS_ROOT', $data);
        exec('cp -a ' . escapeshellarg(dirname(__DIR__, 2) . '/config/') . ' ' . escapeshellarg($data));
        exec('cp -a ' . escapeshellarg(dirname(__DIR__, 2) . '/view/') . ' ' . escapeshellarg($data));

        $I->amOnPage('/');
        $I->see('Welcome to VerteXVaaR.BlueSprints');
    }
}
