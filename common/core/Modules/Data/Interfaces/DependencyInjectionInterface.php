<?php

declare(strict_types=1);

namespace Core\Modules\Data\Interfaces;

interface DependencyInjectionInterface
{
    public function get(string $name): ?object;

    public function set(string|object $dependency, string $name = null): void;
}
