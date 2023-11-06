<?php

namespace VerteXVaaR\BlueDistTest\Helper;

use Codeception\Module;
use VerteXVaaR\BlueAuth\Mvcr\Model\User;
use VerteXVaaR\BlueConfig\Config;
use VerteXVaaR\BlueContainer\Generated\PackageExtras;
use VerteXVaaR\BlueSprints\Mvcr\Repository\Repository;
use VerteXVaaR\BlueSprints\Store\FileStore;

use function password_hash;

use const PASSWORD_ARGON2ID;

class Acceptance extends Module
{
    public function haveUser(string $username, string $password, array $roles = ['user']): void
    {
        $user = new User($username);
        $user->hashedPassword = password_hash($password, PASSWORD_ARGON2ID);
        $user->roles = $roles;
        $packageExtras = new PackageExtras();
        $config = new Config($packageExtras);
        $store = new FileStore($config, $packageExtras);
        $repo = new Repository($store);
        $repo->persist($user);
    }
}
