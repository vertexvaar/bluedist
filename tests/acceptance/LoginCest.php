<?php

declare(strict_types=1);

namespace acceptance;

use VerteXVaaR\BlueDistTest\AcceptanceTester;

use function ini_get;

class LoginCest
{

    public function _before(AcceptanceTester $I)
    {
    }

    // tests
    public function loginAndLogout(AcceptanceTester $I)
    {
        $I->haveUser('admin', 'password', ['user', 'admin']);

        $I->amOnPage('/');

        $I->dontSeeCookie('auth');

        if (ini_get('xdebug.mode') === 'debug') {
            $I->setCookie('XDEBUG_SESSION', 'XDEBUG_ECLIPSE');
        }

        $I->see('Session: (anonymous)');

        $I->amOnPage('/login');
        $I->submitForm('#login', [
            'username' => 'admin',
            'password' => 'password',
        ]);
        // Requires bluesprints debug package
        $I->see('Session: admin');

        $I->seeCookie('auth');

        $I->amOnPage('/logout');

        // Requires bluesprints debug package
        $I->see('Session: (anonymous)');

        $I->dontSeeCookie('auth');
    }
}
