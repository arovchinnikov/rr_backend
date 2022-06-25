<?php

declare(strict_types=1);

namespace Core\Modules\Routing;

use Core\Modules\Data\FileManager;
use Core\Modules\Data\Interfaces\FileManagerInterface;
use Core\Modules\Http\Enums\RequestMethod;
use Core\Modules\Routing\Exceptions\RoutingException;

class RouteCollection
{
    /** @var Route[][] */
    private array $routes;
    private FileManagerInterface $fileManager;

    public function __construct()
    {
        $this->fileManager = new FileManager();
    }

    /**
     * @throws Exceptions\RoutingException
     */
    public function collect(string $path = ROOT . '/common/routes'): void
    {
        $routeFiles = $this->fileManager->scanDir($path, true);

        $this->setFromFiles($routeFiles);
    }

    public function get(RequestMethod $method = null): array
    {
        if (!empty($method)) {
            return $this->routes[$method->value];
        }

        return $this->routes;
    }

    /**
     * @throws Exceptions\RoutingException
     */
    private function setFromFiles(array $files): void
    {
        foreach ($files as $file) {
            if (is_array($file)) {
                $this->setFromFiles($file);
                continue;
            }

            $routes = yaml_parse($this->fileManager->getContent($file));

            foreach ($routes as $route) {
                $this->set($route);
            }
        }
    }

    /**
     * @throws Exceptions\RoutingException
     */
    private function set(array $route): void
    {
        foreach ($route['methods'] as $method) {
            $requestMethod = RequestMethod::tryFrom($method);
            if (empty($requestMethod)) {
                RoutingException::invalidRouteMethod($route['path'], $method);
            }

            $this->routes[$method][] = new Route(
                $route['path'],
                $route['controller'],
                $route['action'],
                RequestMethod::from($method)
            );
        }
    }
}
