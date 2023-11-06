<?php

use function CoStack\Lib\concat_paths;

$root = dirname(__DIR__);
putenv('VXVR_BS_TEST_ROOT=' . $root);
$data = concat_paths($root, 'tests/_data/functional', uniqid('', true));

$dotEnvFile = concat_paths($root, '.env');
if (file_exists($dotEnvFile)) {
    rename($dotEnvFile, $dotEnvFile . '.backup');
}

register_shutdown_function(
    static function () use ($dotEnvFile): void {
        exec('rm -f ' . escapeshellarg($dotEnvFile));
        if (file_exists($dotEnvFile . '.backup')) {
            rename($dotEnvFile . '.backup', $dotEnvFile);
        }
    },
);
file_put_contents(
    $dotEnvFile,
    <<<ENV
APP_ENV=test
APP_DEBUG=true

VXVR_BS_ROOT=$data
ENV,
);

register_shutdown_function(static fn() => exec('rm -rf ' . escapeshellarg($data)));
if (!mkdir($data, 0777, true) && !is_dir($data)) {
    throw new RuntimeException(sprintf('Directory "%s" was not created', $data));
}

putenv('VXVR_BS_ROOT=' . $data);
exec('cp -a ' . escapeshellarg(concat_paths($root, 'config')) . ' ' . escapeshellarg($data));
exec('cp -a ' . escapeshellarg(concat_paths($root, 'view')) . ' ' . escapeshellarg($data));
