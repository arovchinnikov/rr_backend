<?php

declare(strict_types=1);

namespace Core\Modules\Data;

use Core\Modules\Data\Interfaces\DependencyInjectionInterface;

class DI implements DependencyInjectionInterface
{
    private static array $data;

    public function get(string $name): ?object
    {
        if (!empty(self::$data[$name])) {
            return self::$data[$name];
        }

        if (class_exists($name)) {
            $this->set($name);
            return self::$data[$name];
        }

        return null;
    }

    public function set(object|string $dependency, string $name = null): void
    {

        if (is_string($dependency) && class_exists($dependency)) {
            self::$data[$name ?? $dependency] = new $dependency();
        }
    }
}
