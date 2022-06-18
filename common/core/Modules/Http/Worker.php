<?php

declare(strict_types=1);

namespace Core\Modules\Http;

use JsonException;
use Spiral\RoadRunner\Http\PSR7Worker;

class Worker extends PSR7Worker
{
    private HttpFactory $httpFactory;

    public function __construct(HttpFactory $httpFactory)
    {
        $this->httpFactory = $httpFactory;
        $worker = \Spiral\RoadRunner\Worker::create();

        parent::__construct($worker, $httpFactory, $httpFactory, $httpFactory);
    }

    /**
     * @throws JsonException
     */
    public function respondString(string $text): void
    {
        $response = $this->httpFactory->createResponse();
        $response->getBody()->write($text);

        parent::respond($response);
    }
}
