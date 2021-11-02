<?php

declare(strict_types=1);

class FruitCest
{
    public function _before()
    {
        exec('rm -rf ' . escapeshellarg(__DIR__ . '/../../database'));
    }

    public function _after()
    {
        exec('rm -rf ' . escapeshellarg(__DIR__ . '/../../database'));
    }

    public function fruitsAreEditableTest(AcceptanceTester $I)
    {
        $I->amOnPage('/listFruits');
        $I->see('OH WAIT! There is no Fruit yet.');

        $I->click('Create a bunch of fruits');
        $I->see('Hint: click on a fruit to edit it!');
        $I->see('I am a Apple and my color is red');

        $I->click('I am a Apple and my color is red');
        $I->seeInField('name', 'Apple');
        $I->seeInField('color', 'red');

        $I->fillField('color', 'green');
        $I->click('Update it!');

        $I->see('I am a Apple and my color is green');

        $I->click('I am a Apple and my color is green');
        $I->click('Or delete it :)');

        $I->dontSee('I am a Apple and my color is green');
    }
}
