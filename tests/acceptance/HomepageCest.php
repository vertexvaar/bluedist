<?php

declare(strict_types=1);

class HomepageCest
{
    public function specificContentOnHomepageTest(AcceptanceTester $I)
    {
        $I->amOnPage('/');
        $I->see('VerteXVaaR.BlueSprints An experiment gone right :D');
    }
}
