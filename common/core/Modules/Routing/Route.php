<?php

declare(strict_types=1);

namespace Core\Modules\Routing;

use Core\Modules\Http\Enums\RequestMethod;
use Core\Modules\Routing\Exceptions\RoutingException;

class Route
{
    private string $pattern;
    private string $controller;
    private string $action;
    private RequestMethod $method;
    /** @var string[] GET params in uri */
    private array $params = [];

    /**
     * @throws RoutingException
     */
    public function __construct(string $path, string $controller, string $action, RequestMethod $method)
    {
        $this->setPattern($path);
        $this->setEndpoint($controller, $action);
        $this->method = $method;
    }

    public function matches(string $path): bool
    {
        if (preg_match($this->pattern, $path, $rawParams)) {
            $params = [];
            foreach ($rawParams as $name => $value) {
                if (is_string($name)) {
                    $params[$name] = $value;
                }
            }

            $this->params = $params;

            return true;
        }

        return false;
    }

    public function getController(): string
    {
        return $this->controller;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function getMethod(): RequestMethod
    {
        return $this->method;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    private function setPattern(string $path): void
    {
        $path = explode('/', $path);
        $pattern = [];
        foreach ($path as $pathPart) {
            /** Matches [var] */
            if (preg_match("/^\[[-a-z-0-9-_]+\]$/", $pathPart)) {
                $pathPart = str_replace(['[', ']'], '', $pathPart);
                $pathPart = "(?P<" . $pathPart . ">\w+)";
            }
            $pattern[] = $pathPart;
        }

        $pattern = implode('\/', $pattern);
        $this->pattern =  '/^' . $pattern . '$/';
    }

    /**
     * @throws RoutingException
     */
    private function setEndpoint(string $controller, string $action): void
    {
        if (!method_exists($controller, $action)) {
            RoutingException::controllerOrActionNotFound($controller, $action);
        }

        $this->controller = $controller;
        $this->action = $action;
    }
}
