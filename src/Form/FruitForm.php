<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueDist\Form;

use VerteXVaaR\BlueForm\Element\Form;
use VerteXVaaR\BlueForm\Element\Form\Button\Submit;
use VerteXVaaR\BlueForm\Element\Form\Input\Text;
use VerteXVaaR\BlueForm\Element\Form\Security\Csrf;

use function getenv;

class FruitForm extends Form
{
    public function __construct()
    {
        parent::__construct('fruit');

        $this->setChildren(
            new Csrf($this->name, getenv('APP_SECRET')),
            (new Text('name'))->setLabel('Name'),
            (new Text('color'))->setLabel('Color'),
            (new Submit('save'))->setLabel('Save Fruit'),
        );
    }
}
