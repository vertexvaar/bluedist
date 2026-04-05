<?php

namespace VerteXVaaR\BlueAuth\Dto;

use VerteXVaaR\BlueSprints\Mvcr\Model\Entity;

class Login extends Entity
{
    public string $username = '';
    public string $password = '';
}
