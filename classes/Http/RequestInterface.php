<?php
declare(strict_types=1);
namespace VerteXVaaR\BlueSprints\Http;

/**
 * Class RequestInterface
 */
interface RequestInterface
{
    const HTTP_METHOD_GET = 'GET';
    const HTTP_METHOD_HEAD = 'HEAD';
    const HTTP_METHOD_POST = 'POST';
    const HTTP_METHOD_PUT = 'PUT';
    const HTTP_METHOD_DELETE = 'DELETE';
}
