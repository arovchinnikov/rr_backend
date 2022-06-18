<?php

declare(strict_types=1);

namespace Core\Modules\Http\Components;

use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use RuntimeException;

class UploadedFile implements UploadedFileInterface
{
    private const ERRORS = [
        UPLOAD_ERR_OK => 1,
        UPLOAD_ERR_INI_SIZE => 1,
        UPLOAD_ERR_FORM_SIZE => 1,
        UPLOAD_ERR_PARTIAL => 1,
        UPLOAD_ERR_NO_FILE => 1,
        UPLOAD_ERR_NO_TMP_DIR => 1,
        UPLOAD_ERR_CANT_WRITE => 1,
        UPLOAD_ERR_EXTENSION => 1,
    ];

    private string $clientFilename;
    private string $clientMediaType;
    private int $error;
    private ?string $file;
    private bool $moved = false;
    private int $size;
    private ?StreamInterface $stream;

    public function __construct(
        mixed $streamOrFile,
        int $size,
        int $errorStatus,
        string $clientFilename = null,
        string $clientMediaType = null
    ) {
        if (null !== $clientFilename && !is_string($clientFilename)) {
            throw new InvalidArgumentException('Upload file client filename must be a string or null');
        }

        if (null !== $clientMediaType && !is_string($clientMediaType)) {
            throw new InvalidArgumentException('Upload file client media type must be a string or null');
        }

        $this->error = $errorStatus;
        $this->size = $size;
        $this->clientFilename = $clientFilename;
        $this->clientMediaType = $clientMediaType;

        if (UPLOAD_ERR_OK === $this->error) {
            if (is_string($streamOrFile) && '' !== $streamOrFile) {
                $this->file = $streamOrFile;
            } elseif (is_resource($streamOrFile)) {
                $this->stream = Stream::create($streamOrFile);
            } elseif ($streamOrFile instanceof StreamInterface) {
                $this->stream = $streamOrFile;
            } else {
                throw new InvalidArgumentException('Invalid stream or file provided for UploadedFile');
            }
        }
    }


    private function validateActive(): void
    {
        if (UPLOAD_ERR_OK !== $this->error) {
            throw new RuntimeException('Cannot retrieve stream due to upload error');
        }

        if ($this->moved) {
            throw new RuntimeException('Cannot retrieve stream after it has already been moved');
        }
    }

    public function getStream(): StreamInterface
    {
        $this->validateActive();

        if ($this->stream instanceof StreamInterface) {
            return $this->stream;
        }

        if (false === $resource = @fopen($this->file, 'r')) {
            throw new RuntimeException(
                'The file "' . $this->file . '" cannot be opened: ' . error_get_last()['message'] ?? ''
            );
        }

        return Stream::create($resource);
    }

    public function moveTo(mixed $targetPath): void
    {
        $this->validateActive();

        if (!is_string($targetPath) || '' === $targetPath) {
            throw new InvalidArgumentException(
                'Invalid path provided for move operation; must be a non-empty string'
            );
        }

        if (null !== $this->file) {
            $this->moved = 'cli' === PHP_SAPI ?
                @rename($this->file, $targetPath) : @move_uploaded_file($this->file, $targetPath);

            if (false === $this->moved) {
                throw new RuntimeException(
                    'Uploaded file could not be moved to "' .
                    $targetPath . '": %s' . error_get_last()['message'] ?? ''
                );
            }
        } else {
            $stream = $this->getStream();
            if ($stream->isSeekable()) {
                $stream->rewind();
            }

            if (false === $resource = @fopen($targetPath, 'w')) {
                throw new RuntimeException(
                    'The file "' . $targetPath . '" cannot be opened: ' . error_get_last()['message'] ?? ''
                );
            }

            $dest = Stream::create($resource);

            while (!$stream->eof()) {
                if (!$dest->write($stream->read(1048576))) {
                    break;
                }
            }

            $this->moved = true;
        }
    }

    public function getSize(): int
    {
        return $this->size;
    }

    public function getError(): int
    {
        return $this->error;
    }

    public function getClientFilename(): ?string
    {
        return $this->clientFilename;
    }

    public function getClientMediaType(): ?string
    {
        return $this->clientMediaType;
    }
}
