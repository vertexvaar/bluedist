<?php

namespace VerteXVaaR\BlueAuth\Form;

use CoStack\Lib\Enum\Direction;
use VerteXVaaR\BlueForm\Element\Form;
use VerteXVaaR\BlueForm\Element\Form\Button\Submit;
use VerteXVaaR\BlueForm\Element\Form\Input\Password;
use VerteXVaaR\BlueForm\Element\Form\Input\Text;
use VerteXVaaR\BlueForm\Element\Form\Security\Csrf;
use VerteXVaaR\BlueForm\Element\Support\Icon;
use VerteXVaaR\BlueValidation\Rule\MinLength;
use VerteXVaaR\BlueValidation\Rule\Required;

use function getenv;

class LoginForm extends Form
{
    public function __construct()
    {
        parent::__construct('form');

        $this->setChildren(
            new Csrf($this->name, getenv('APP_SECRET')),
            new Text('username')
                ->setLabel('Username')
                ->setPlaceholder('e.g. admin')
                ->setIcon(new Icon('fas fa-user', Direction::Left))
                ->addValidation(new Required()),
            new Password('password')
                ->setLabel('Password')
                ->setPlaceholder('********')
                ->setIcon(new Icon('fas fa-lock', Direction::Left))
                ->addValidation(new Required(), new MinLength(8)),
            new Submit('login')
                ->setLabel('Login')
                ->setIsFullWidth(),
        );
    }
}
