<?php

$root = dirname(__DIR__);
$data = $root . '/tests/_data/functional/' . uniqid('', true) . '/';

register_shutdown_function(static fn() => exec('rm -f ' . escapeshellarg($root . '/.env')));
file_put_contents($root . '/.env', "VXVR_BS_CONTEXT=Dev-Testing\nVXVR_BS_ROOT=" . $data);

register_shutdown_function(static fn() => exec('rm -rf ' . escapeshellarg($data)));
if (!mkdir($data, 0777, true) && !is_dir($data)) {
    throw new RuntimeException(sprintf('Directory "%s" was not created', $data));
}

putenv('VXVR_BS_ROOT=' . $data);
exec('cp -a ' . escapeshellarg($root . '/config/') . ' ' . escapeshellarg($data));
exec('cp -a ' . escapeshellarg($root . '/view/') . ' ' . escapeshellarg($data));
