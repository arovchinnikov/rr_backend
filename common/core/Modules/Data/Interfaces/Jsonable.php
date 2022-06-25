<?php

declare(strict_types=1);

namespace Core\Modules\Data\Interfaces;

interface Jsonable
{
    public function toJson(): string;
}
