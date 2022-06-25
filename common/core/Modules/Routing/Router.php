<?php

declare(strict_types=1);

namespace Core\Modules\Routing;

use Core\Base\Interfaces\ControllerInterface;
use Core\Modules\Data\Exceptions\ServiceContainerException;
use Core\Modules\Data\Interfaces\Arrayable;
use Core\Modules\Data\Interfaces\Jsonable;
use Core\Modules\Data\Interfaces\ServiceContainerInterface;
use Core\Modules\Data\ServiceContainer;
use Core\Modules\Http\Enums\RequestMethod;
use Core\Modules\Http\HttpFactory;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Router
{
    private HttpFactory $httpFactory;
    private RouteCollection $routeCollection;
    private ServiceContainerInterface $serviceContainer;

    /**
     * @throws Exceptions\RoutingException
     */
    public function __construct(HttpFactory $httpFactory)
    {
        $this->serviceContainer = new ServiceContainer();
        $routes = new RouteCollection();
        $routes->collect();
        $this->httpFactory = $httpFactory;
        $this->routeCollection = $routes;
    }

    public function dispatch(RequestInterface $request): ResponseInterface
    {
        $requestPath = $request->getUri()->getPath();
        $requestMethod = RequestMethod::tryFrom($request->getMethod());
        $route = $this->getMatch($requestPath, $requestMethod);

        if (empty($route)) {
            return $this->httpFactory->createNotFoundResponse();
        }

        $result = $this->getRouteJsonResult($route);
        return $this->httpFactory->createJsonResponse($result);
    }

    private function getMatch(string $path, RequestMethod $method = null): ?Route
    {
        $routes = $this->routeCollection->get($method);

        foreach ($routes as $route) {
            if ($route->matches($path)) {
                return $route;
            }
        }

        return null;
    }

    /**
     * @throws ServiceContainerException
     */
    private function getRouteJsonResult(Route $route): string
    {
        $routeController = $route->getController();
        $routeAction = $route->getAction();

        /** @var ControllerInterface $controller */
        $controller = new $routeController();
        $actionArgs = $this->serviceContainer->getActionServices($routeController, $routeAction);
        $result = $controller->$routeAction(...$actionArgs);

        if (is_object($result)) {
            $interfaces = class_implements($result);

            foreach ($interfaces as $interface) {
                if ($interface === Jsonable::class) {
                    /** @var Jsonable $result */
                    return $result->toJson();
                } elseif ($interface === Arrayable::class) {
                    /** @var Arrayable $result */
                    return json_encode($result->toArray());
                }
            }

            return json_encode(['error' => 'Invalid controller return']);
        }

        return json_encode($result ?? ['error' => 'Empty return ']);
    }
}
