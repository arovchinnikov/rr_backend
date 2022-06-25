<?php

namespace Core\Modules\Data\Interfaces;

interface FileManagerInterface
{
    public function getContent(string $path): ?string;

    public function exists(string $path): bool;

    public function scanDir(string $path, bool $recurrent = false): ?array;
}
