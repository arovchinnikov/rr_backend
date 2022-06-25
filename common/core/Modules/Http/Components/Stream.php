<?php

namespace Core\Modules\Http\Components;

use Core\Modules\Http\Exceptions\HttpException;
use Error;
use Psr\Http\Message\StreamInterface;
use Throwable;

class Stream implements StreamInterface
{
    /** @var resource|null */
    private mixed $stream;

    private bool $seekable;
    private bool $readable;
    private bool $writable;
    private mixed $uri;
    private ?int $size;

    /** @var array Hash of readable and writable stream types */
    private const READ_WRITE_HASH = [
        'read' => [
            'r' => true, 'w+' => true, 'r+' => true, 'x+' => true, 'c+' => true,
            'rb' => true, 'w+b' => true, 'r+b' => true, 'x+b' => true,
            'c+b' => true, 'rt' => true, 'w+t' => true, 'r+t' => true,
            'x+t' => true, 'c+t' => true, 'a+' => true,
        ],
        'write' => [
            'w' => true, 'w+' => true, 'rw' => true, 'r+' => true, 'x+' => true,
            'c+' => true, 'wb' => true, 'w+b' => true, 'r+b' => true,
            'x+b' => true, 'c+b' => true, 'w+t' => true, 'r+t' => true,
            'x+t' => true, 'c+t' => true, 'a' => true, 'a+' => true,
        ],
    ];

    /**
     * @throws HttpException
     */
    public static function create(mixed $body = ''): StreamInterface
    {
        if ($body instanceof StreamInterface) {
            return $body;
        }

        if (is_string($body)) {
            $resource = fopen('php://temp', 'rw+');
            fwrite($resource, $body);
            $body = $resource;
        }

        if (is_resource($body)) {
            $new = new self();
            $new->stream = $body;
            $meta = stream_get_meta_data($new->stream);
            $new->seekable = $meta['seekable'] && 0 === \fseek($new->stream, 0, \SEEK_CUR);
            $new->readable = isset(self::READ_WRITE_HASH['read'][$meta['mode']]);
            $new->writable = isset(self::READ_WRITE_HASH['write'][$meta['mode']]);

            return $new;
        }

        HttpException::streamCreateError();
    }

    public function __destruct()
    {
        $this->close();
    }

    /**
     * @throws HttpException
     */
    public function __toString(): string
    {
        try {
            if ($this->isSeekable()) {
                $this->seek(0);
            }

            return $this->getContents();
        } catch (Throwable $e) {
            if (PHP_VERSION_ID >= 70400) {
                throw $e;
            }

            restore_error_handler();

            if ($e instanceof Error) {
                return trigger_error((string) $e, E_USER_ERROR);
            }

            return '';
        }
    }

    public function close(): void
    {
        if (isset($this->stream)) {
            if (is_resource($this->stream)) {
                fclose($this->stream);
            }
            $this->detach();
        }
    }

    public function detach(): mixed
    {
        if (!isset($this->stream)) {
            return null;
        }

        $result = $this->stream;
        unset($this->stream);
        $this->size = $this->uri = null;
        $this->readable = $this->writable = $this->seekable = false;

        return $result;
    }

    private function getUri(): mixed
    {
        if (false !== $this->uri) {
            $this->uri = $this->getMetadata('uri') ?? false;
        }

        return $this->uri;
    }

    public function getSize(): ?int
    {
        if (null !== $this->size) {
            return $this->size;
        }

        if (!isset($this->stream)) {
            return null;
        }

        if ($uri = $this->getUri()) {
            clearstatcache(true, $uri);
        }

        $stats = fstat($this->stream);
        if (isset($stats['size'])) {
            $this->size = $stats['size'];

            return $this->size;
        }

        return null;
    }

    /**
     * @throws HttpException
     */
    public function tell(): int
    {
        if (!isset($this->stream)) {
            HttpException::detachedStream();
        }

        if (false === $result = @ftell($this->stream)) {
            HttpException::determineStreamPositionError(error_get_last()['message'] ?? '');
        }

        return $result;
    }

    public function eof(): bool
    {
        return !isset($this->stream) || feof($this->stream);
    }

    public function isSeekable(): bool
    {
        return $this->seekable;
    }

    /**
     * @throws HttpException
     */
    public function seek($offset, $whence = SEEK_SET): void
    {
        if (!isset($this->stream)) {
            HttpException::detachedStream();
        }

        if (!$this->seekable) {
            HttpException::notSeekableStream();
        }

        if (-1 === fseek($this->stream, $offset, $whence)) {
            HttpException::seekStreamPositionError($offset, var_export($whence, true));
        }
    }

    /**
     * @throws HttpException
     */
    public function rewind(): void
    {
        $this->seek(0);
    }

    public function isWritable(): bool
    {
        return $this->writable;
    }

    /**
     * @throws HttpException
     */
    public function write($string): int
    {
        if (!isset($this->stream)) {
            HttpException::detachedStream();
        }

        if (!$this->writable) {
            HttpException::notWritableStream();
        }

        $this->size = null;

        if (false === $result = @fwrite($this->stream, $string)) {
            HttpException::writeStreamError(error_get_last()['message'] ?? '');
        }

        return $result;
    }

    public function isReadable(): bool
    {
        return $this->readable;
    }

    /**
     * @throws HttpException
     */
    public function read($length): string
    {
        if (!isset($this->stream)) {
            HttpException::detachedStream();
        }

        if (!$this->readable) {
            HttpException::notReadableStream();
        }

        if (false === $result = @fread($this->stream, $length)) {
            HttpException::readStreamError(error_get_last()['message'] ?? '');
        }

        return $result;
    }

    /**
     * @throws HttpException
     */
    public function getContents(): string
    {
        if (!isset($this->stream)) {
            HttpException::detachedStream();
        }

        if (false === $contents = @stream_get_contents($this->stream)) {
            HttpException::readStreamContentError(error_get_last()['message'] ?? '');
        }

        return $contents;
    }

    public function getMetadata($key = null): mixed
    {
        if (!isset($this->stream)) {
            return $key ? null : [];
        }

        $meta = stream_get_meta_data($this->stream);

        if (null === $key) {
            return $meta;
        }

        return $meta[$key] ?? null;
    }
}
