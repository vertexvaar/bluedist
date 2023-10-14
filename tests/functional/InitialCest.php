<?php

namespace functional;

use VerteXVaaR\BlueDistTest\FunctionalTester;

class InitialCest
{
    public function rootPageTest(FunctionalTester $I)
    {
        $I->amOnPage('/');
        $I->see('Welcome to VerteXVaaR.BlueSprints');
    }
}
