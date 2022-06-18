<?php

declare(strict_types=1);

namespace Core\Modules\Http\Components;

use Core\Modules\Http\Components\Traits\MessageTrait;
use Core\Modules\Http\Enums\ResponseCode;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class Response implements ResponseInterface
{
    use MessageTrait;

    private string $reasonPhrase = '';
    private int $statusCode;

    /**
     * @param int $status Status code
     * @param array $headers Response headers
     * @param string|resource|StreamInterface|null $body Response body
     * @param string $version Protocol version
     * @param string|null $reason Reason phrase
     */
    public function __construct(
        int $status = 200,
        array $headers = [],
        mixed $body = null,
        string $version = '1.1',
        string $reason = null
    ) {
        if ('' !== $body && null !== $body) {
            $this->stream = Stream::create($body);
        }

        $this->statusCode = $status;
        $this->setHeaders($headers);
        if (null === $reason && !empty(ResponseCode::from($status))) {
            $this->reasonPhrase = ResponseCode::from($status)->message();
        } else {
            $this->reasonPhrase = $reason ?? '';
        }

        $this->protocol = $version;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getReasonPhrase(): string
    {
        return $this->reasonPhrase;
    }

    public function withStatus($code, $reasonPhrase = ''): self
    {
        if (!is_int($code) && !is_string($code)) {
            throw new InvalidArgumentException('Status code has to be an integer');
        }

        $code = (int) $code;
        if ($code < 100 || $code > 599) {
            throw new InvalidArgumentException(
                'Status code has to be an integer between 100 and 599. A status code of ' . $code . ' was given'
            );
        }

        $new = clone $this;
        $new->statusCode = $code;
        if ((null === $reasonPhrase || '' === $reasonPhrase) && !empty(ResponseCode::from($new->statusCode))) {
            $reasonPhrase = ResponseCode::from($new->statusCode)->message();
        }
        $new->reasonPhrase = $reasonPhrase;

        return $new;
    }
}
