<?php

declare(strict_types=1);

namespace Core\Modules\Data;

use Core\Base\Interfaces\ServiceInterface;
use Core\Modules\Data\Exceptions\ServiceContainerException;
use Core\Modules\Data\Interfaces\DependencyInjectionInterface;
use Core\Modules\Data\Interfaces\ServiceContainerInterface;
use ReflectionClass;
use ReflectionException;

class ServiceContainer implements ServiceContainerInterface
{
    private DependencyInjectionInterface $di;

    public function __construct()
    {
        $this->di = new DI();
    }

    /**
     * @throws ServiceContainerException
     * @return ?ServiceInterface[]
     */
    public function getActionServices(string $controllerName, string $actionName): ?array
    {
        try {
            $reflection = new ReflectionClass($controllerName);
            $action = $reflection->getMethod($actionName);

            $params = [];
            foreach ($action->getParameters() as $param) {
                $paramType = $param->getType()->getName();
                if (class_exists($paramType) && in_array(ServiceInterface::class, class_implements($paramType))) {
                    $params[] = $this->di->get($paramType);
                }
            }

            return $params ?? null;
        } catch (ReflectionException $e) {
            ServiceContainerException::reflectionException($e);
        }
    }
}
