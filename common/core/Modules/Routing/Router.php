<?php

declare(strict_types=1);

namespace Core\Modules\Routing;

use Core\Modules\Http\HttpFactory;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Router
{
    private HttpFactory $httpFactory;

    private array $routes;

    public function __construct(HttpFactory $httpFactory)
    {
        $this->httpFactory = $httpFactory;
    }

    public function dispatch(RequestInterface $request): ResponseInterface
    {
        $response = $this->httpFactory
            ->createResponse()
            ->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode(['message' => 'Hello world']));

        return $response;
    }
}
