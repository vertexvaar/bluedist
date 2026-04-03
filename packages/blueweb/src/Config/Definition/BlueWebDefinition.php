<?php

declare(strict_types=1);

namespace VerteXVaaR\BlueWeb\Config\Definition;

use VerteXVaaR\BlueConfig\Definition\Definition;
use VerteXVaaR\BlueConfig\Structure\ArrayNode;
use VerteXVaaR\BlueConfig\Structure\Node;
use VerteXVaaR\BlueConfig\Structure\ObjectNode;
use VerteXVaaR\BlueConfig\Structure\RootNode;

readonly class BlueWebDefinition implements Definition
{
    public function get(): Node
    {
        return new RootNode(
            'blueweb JSON schema',
            'Schema for the configuration YAML',
            [
                new ArrayNode(
                    'trustedServerNames',
                    'Trusted server names',
                    'Names or regular expressions to match $_SERVER[\'SERVER_NAME\'] which is used for example in CORS header. Use [\'*\'] for any and [\'SERVER_NAME\'] to use the current value of $_SERVER[\'SERVER_NAME\'].',
                    ['SERVER_NAME'],
                ),
                new ObjectNode(
                    'cors',
                    'CORS header configuration',
                    'Control your CORS header',
                    [
                        new ArrayNode(
                            'allowedOrigins',
                            'Allowed origins',
                            'A list of domains or special keywords which determine the origin. Use "*" (not recommended) for all and "_trustedServerName" for the current server name if trusted',
                            ['_trustedServerName'],
                        ),
                        new ArrayNode(
                            'allowedMethods',
                            'Allowed Methods',
                            'A list HTTP methods that are allowed. Default: [ALL]',
                            ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
                        ),
                        new ArrayNode(
                            'allowedHeaders',
                            'Allowed origins',
                            'A list of header names which are allowed in cross origin requests. Default [NONE]',
                            [],
                        ),
                    ],
                ),
            ],
        );
    }
}
