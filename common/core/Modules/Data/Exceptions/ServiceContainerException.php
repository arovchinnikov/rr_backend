<?php

declare(strict_types=1);

namespace Core\Modules\Data\Exceptions;

use Core\Base\Exceptions\CoreException;
use ReflectionException;

class ServiceContainerException extends CoreException
{
    /**
     * @throws ServiceContainerException
     */
    public static function reflectionException(ReflectionException $exception)
    {
        throw new self($exception->getMessage());
    }
}
