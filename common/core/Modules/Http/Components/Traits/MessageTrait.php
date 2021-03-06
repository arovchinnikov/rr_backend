<?php

declare(strict_types=1);

namespace Core\Modules\Http\Components\Traits;

use Core\Modules\Http\Components\Stream;
use Core\Modules\Http\Exceptions\HttpException;
use Psr\Http\Message\StreamInterface;

trait MessageTrait
{
    private array $headers = [];
    private array $headerNames = [];
    private string $protocol = '1.1';
    private ?StreamInterface $stream;

    public function getProtocolVersion(): string
    {
        return $this->protocol;
    }

    public function withProtocolVersion($version): self
    {
        if ($this->protocol === $version) {
            return $this;
        }

        $new = clone $this;
        $new->protocol = $version;

        return $new;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function hasHeader($header): bool
    {
        return isset($this->headerNames[strtr($header, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz')]);
    }

    public function getHeader($header): array
    {
        $header = strtr($header, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz');
        if (!isset($this->headerNames[$header])) {
            return [];
        }

        $header = $this->headerNames[$header];

        return $this->headers[$header];
    }

    public function getHeaderLine($header): string
    {
        return implode(', ', $this->getHeader($header));
    }

    /**
     * @throws HttpException
     */
    public function withHeader($header, $value): self
    {
        $value = $this->validateAndTrimHeader($header, $value);
        $normalized = strtr($header, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz');

        $new = clone $this;
        if (isset($new->headerNames[$normalized])) {
            unset($new->headers[$new->headerNames[$normalized]]);
        }
        $new->headerNames[$normalized] = $header;
        $new->headers[$header] = $value;

        return $new;
    }

    /**
     * @throws HttpException
     */
    public function withAddedHeader($header, $value): self
    {
        if (!is_string($header) || '' === $header) {
            HttpException::invalidHeaderName();
        }

        $new = clone $this;
        $new->setHeaders([$header => $value]);

        return $new;
    }

    public function withoutHeader($header): self
    {
        $normalized = strtr($header, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz');
        if (!isset($this->headerNames[$normalized])) {
            return $this;
        }

        $header = $this->headerNames[$normalized];
        $new = clone $this;
        unset($new->headers[$header], $new->headerNames[$normalized]);

        return $new;
    }

    /**
     * @throws HttpException
     */
    public function getBody(): StreamInterface
    {
        if (empty($this->stream)) {
            $this->stream = Stream::create('');
        }

        return $this->stream;
    }

    public function withBody(StreamInterface $body): self
    {
        if ($body === $this->stream) {
            return $this;
        }

        $new = clone $this;
        $new->stream = $body;

        return $new;
    }

    /**
     * @throws HttpException
     */
    private function setHeaders(array $headers): void
    {
        foreach ($headers as $header => $value) {
            if (is_int($header)) {
                $header = (string) $header;
            }
            $value = $this->validateAndTrimHeader($header, $value);
            $normalized = strtr($header, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz');
            if (isset($this->headerNames[$normalized])) {
                $header = $this->headerNames[$normalized];
                $this->headers[$header] = array_merge($this->headers[$header], $value);
            } else {
                $this->headerNames[$normalized] = $header;
                $this->headers[$header] = $value;
            }
        }
    }

    /**
     * @throws HttpException
     */
    private function validateAndTrimHeader(string $header, array|string $values): array
    {
        if (1 !== preg_match("@^[!#$%&'*+.^_`|~0-9A-Za-z-]+$@", $header)) {
            HttpException::invalidHeaderName();
        }

        if (!is_array($values)) {
            if (
                (!is_numeric($values) && !is_string($values))
                || 1 !== preg_match("@^[ \t\x21-\x7E\x80-\xFF]*$@", (string) $values)
            ) {
                HttpException::invalidHeaderValues();
            }

            return [trim((string) $values, " \t")];
        }

        if (empty($values)) {
            HttpException::headerValuesIsEmpty();
        }

        $returnValues = [];
        foreach ($values as $v) {
            if (
                (!is_numeric($v) && !is_string($v))
                || 1 !== preg_match("@^[ \t\x21-\x7E\x80-\xFF]*$@", (string) $v)
            ) {
                HttpException::invalidHeaderValues();
            }

            $returnValues[] = trim((string) $v, " \t");
        }

        return $returnValues;
    }
}
