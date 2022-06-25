<?php

declare(strict_types=1);

namespace Core\Modules\Data;

use Core\Modules\Data\Interfaces\FileManagerInterface;

class FileManager implements FileManagerInterface
{
    public function getContent(string $path): ?string
    {
        return file_get_contents($path) ?? null;
    }

    public function exists(string $path): bool
    {
        return file_exists($path);
    }

    public function scanDir(string $path, bool $recurrent = false): ?array
    {
        if (!$this->exists($path)) {
            return null;
        }

        $dir = array_diff(scandir($path), array('..', '.'));

        $scanData = [];
        foreach ($dir as $key => $file) {
            $scanData[$key] = $path . '/' . $file;
        }

        if (!$recurrent) {
            return array_values($scanData);
        }

        $fullScanData = [];
        foreach ($dir as $key => $file) {
            if (is_dir($path . '/' . $file)) {
                $fullScanData[$key] = $this->scanDir($path . '/' . $file, true);
            } else {
                $fullScanData[$key] = $path . '/' . $file;
            }
        }

        return array_values($fullScanData);
    }
}
