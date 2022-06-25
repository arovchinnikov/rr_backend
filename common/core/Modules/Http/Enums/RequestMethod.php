<?php

declare(strict_types=1);

namespace Core\Modules\Http\Enums;

enum RequestMethod: string
{
    case get = 'GET';
    case post = 'POST';
    case put = 'PUT';
    case patch = 'PATCH';
    case delete = 'DELETE';
    case copy = 'COPY';
    case head = 'HEAD';
    case options = 'OPTIONS';
    case link = 'LINK';
    case unlink = 'UNLINK';
    case purge = 'PURGE';
    case lock = 'LOCK';
    case unlock = 'UNLOCK';
    case propfind = 'PROPFIND';
    case view = 'VIEW';
}
