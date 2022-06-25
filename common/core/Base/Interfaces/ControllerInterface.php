<?php

declare(strict_types=1);

namespace Core\Base\Interfaces;

use Core\Modules\Data\Interfaces\DependencyInjectionInterface;

interface ControllerInterface
{
    public function getDi(): DependencyInjectionInterface;
}
