# VerteXVaaR.Bluesprints - Routing

## Abstract

Routing is an essential part of nearly any framework.
Routing describes the concept to connect a certain URL or pattern of URL to a predefined controller and action.
The simplest route configuration you can define is a "catch-all":

```php
<?php
use VerteXVaaR\BlueDist\Controller\Welcome;
use VerteXVaaR\BlueSprints\Http\Request;

return [
    Request::HTTP_METHOD_GET => [
        '.*' => [
            'controller' => Welcome::class,
            'action' => 'index',
        ],
    ],
];
```

## Syntax

There is no special syntax by intention.
Many routing packages have a a billion options to choose from and sometimes even more ways to configure your routes.
This one is intentionally left as simple as possible and is therefore just a 2-dimensional array.

* 1st level: Type of the HTTP request (`GET`, `POST`, ...)
* 2nd level: PCRE regex to match against the current URL
* 3rd level: `controller` and `action` to execute if the route matches

The order of the routes is respected by the Router, so if you put the catch-all first any other route will not have an effect.
You should always have a catch-all which is the last rule in your configuration to prevent Routing errors (and also to implement your 404 behaviour).
