<?php

declare(strict_types=1);

namespace Core\Modules\Routing\Exceptions;

use Core\Base\Exceptions\CoreException;

class RoutingException extends CoreException
{
    /**
     * @throws RoutingException
     */
    public static function controllerOrActionNotFound(string $controllerName, string $actionName): void
    {
        throw new self('Controller - "' . $controllerName . '" or Action - "' . $actionName . '" not found.');
    }

    /**
     * @throws RoutingException
     */
    public static function invalidRouteMethod(string $path, string $method): void
    {
        throw new self('Invalid route method ' . $path . ' - ' . $method);
    }
}
