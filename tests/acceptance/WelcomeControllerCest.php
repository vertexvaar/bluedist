<?php

namespace VerteXVaaR\Acceptance;

use VerteXVaaR\BlueDistTest\AcceptanceTester;

use function ini_get;

class WelcomeControllerCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    // tests
    public function createInitialFruitsTest(AcceptanceTester $I)
    {
        $I->haveUser('admin', 'password', ['user', 'admin']);

        $I->amOnPage('/');

        if (ini_get('xdebug.mode') === 'debug') {
            $I->setCookie('XDEBUG_SESSION', 'XDEBUG_ECLIPSE');
        }

        $I->see('Welcome to VerteXVaaR.BlueSprints');

        $I->click('follow me');
        $I->see('OH WAIT! There is no Fruit yet.');

        $I->click('Create a bunch of fruits');

        $I->click('I am a Apple and my color is red');
        $I->see('Change a Apple');

        $I->submitForm('[action="updateFruit"]', [
            'color' => 'green-red',
        ]);

        $I->see('Login');
        $I->submitForm('#login', [
            'username' => 'admin',
            'password' => 'password',
        ]);

        $I->see('I am a Apple and my color is green-red');
    }

    // tests
    public function deleteAllFruits(AcceptanceTester $I)
    {
        $I->haveUser('admin', 'password', ['user', 'admin']);

        $I->amOnPage('/');

        if (ini_get('xdebug.mode') === 'debug') {
            $I->setCookie('XDEBUG_SESSION', 'XDEBUG_ECLIPSE');
        }

        $I->click('follow me');
        $I->see('OH WAIT! There is no Fruit yet.');

        $I->click('Create a bunch of fruits');

        $I->see('I am a Apple and my color is red');

        $I->click('Delete all fruits');

        $I->see('Login');
        $I->submitForm('#login', [
            'username' => 'admin',
            'password' => 'password',
        ]);

        $I->see('OH WAIT! There is no Fruit yet.');
    }
}
