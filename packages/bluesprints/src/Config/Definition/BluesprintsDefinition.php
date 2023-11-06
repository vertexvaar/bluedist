<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueSprints\Config\Definition;

use VerteXVaaR\BlueConfig\Definition\Definition;
use VerteXVaaR\BlueConfig\Structure\Node;
use VerteXVaaR\BlueConfig\Structure\RootNode;
use VerteXVaaR\BlueConfig\Structure\StringNode;

use function decoct;
use function str_pad;
use function str_split;
use function umask;

use const STR_PAD_LEFT;

class BluesprintsDefinition implements Definition
{
    public function get(): Node
    {
        return new RootNode(
            'bluedist JSON schema',
            'Schema for the configuration YAML',
            [
                new StringNode(
                    'filePermissions',
                    'File Permission Mask',
                    'Permission to set for new files.',
                    $this->getUmask(),
                ),
                new StringNode(
                    'folderPermissions',
                    'Folder Permission Mask',
                    'Permission to set for new folders.',
                    $this->getUmask(),
                ),
            ],
        );
    }

    protected function getUmask(): string
    {
        $result = '';
        $string = decoct(umask());
        foreach (str_split($string) as $number) {
            $result .= 7 - $number;
        }
        $result = str_pad($result, 3, '7', STR_PAD_LEFT);
        return str_pad($result, 4, '0', STR_PAD_LEFT);
    }
}
