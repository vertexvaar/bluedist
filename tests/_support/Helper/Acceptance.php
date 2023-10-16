<?php

namespace VerteXVaaR\BlueDistTest\Helper;

use Codeception\Module;
use VerteXVaaR\BlueAuth\Mvcr\Model\User;
use VerteXVaaR\BlueSprints\Environment\Config;
use VerteXVaaR\BlueSprints\Environment\Paths;
use VerteXVaaR\BlueSprints\Mvcr\Repository\Repository;
use VerteXVaaR\BlueSprints\Store\FileStore;

use function password_hash;

use const PASSWORD_ARGON2ID;

class Acceptance extends Module
{
    public function haveUser(string $username, string $password): void
    {
        $user = new User($username);
        $user->hashedPassword = password_hash($password, PASSWORD_ARGON2ID);
        $paths = new Paths(
            'var/logs',
            'var/locks',
            'var/cache',
            'var/database',
            'config',
            'view',
            'translation',
        );
        $config = new Config();
        $store = new FileStore($paths, $config);
        $repo = new Repository($store);
        $repo->persist($user);
    }
}
