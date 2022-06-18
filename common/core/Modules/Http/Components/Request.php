<?php

declare(strict_types=1);

namespace Core\Modules\Http\Components;

use Core\Modules\Http\Components\Traits\MessageTrait;
use Core\Modules\Http\Components\Traits\RequestTrait;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

class Request implements RequestInterface
{
    use MessageTrait;
    use RequestTrait;

    public function __construct(
        string $method,
        string|UriInterface $uri,
        array $headers = [],
        mixed $body = null,
        string $version = '1.1'
    ) {
        if (!($uri instanceof UriInterface)) {
            $uri = new Uri($uri);
        }

        $this->method = $method;
        $this->uri = $uri;
        $this->setHeaders($headers);
        $this->protocol = $version;

        if (!$this->hasHeader('Host')) {
            $this->updateHostFromUri();
        }

        if ('' !== $body && null !== $body) {
            $this->stream = Stream::create($body);
        }
    }
}
