<?php

declare(strict_types=1);

namespace Core;

use Core\Modules\Http\HttpFactory;
use Core\Modules\Http\Worker;
use Core\Modules\Routing\Router;
use JsonException;
use Throwable;

class App
{
    private static Worker $worker;
    private HttpFactory $factory;
    private Router $router;

    public function __construct()
    {
        $this->factory = new HttpFactory();
        $this->router = new Router($this->factory);
        self::$worker = new Worker($this->factory);
    }

    /**
     * @throws JsonException
     */
    public function run(): void
    {
        while ($request = self::$worker->waitRequest()) {
            try {
                $response = $this->router->dispatch($request);
                self::$worker->respond($response);
            } catch (Throwable $e) {
                /** Display an exception */
                self::$worker->respondString((string)$e);
                self::$worker->getWorker()->error((string)$e);
            }
        }
    }

    public static function getWorker(): Worker|null
    {
        if (isset(self::$worker)) {
            return self::$worker;
        }

        return null;
    }
}
