<?php

declare(strict_types=1);

namespace Core\Base;

use Core\Base\Interfaces\ControllerInterface;
use Core\Modules\Data\DI;
use Core\Modules\Data\Interfaces\DependencyInjectionInterface;

class Controller implements ControllerInterface
{
    private DependencyInjectionInterface $di;

    public function __construct()
    {
        $this->di = new DI();
    }

    public function getDi(): DependencyInjectionInterface
    {
        return $this->di;
    }
}
