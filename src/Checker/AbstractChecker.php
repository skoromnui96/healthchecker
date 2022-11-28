<?php

declare(strict_types=1);

namespace EGlobal\HealthCheck\Checker;

use Eglobal\Healthcheck\Factory\ResponseFactory;

abstract class AbstractChecker implements CheckerInterface
{
    protected string $serviceName;
    protected ResponseFactory $responseFactory;

    public function __construct(string $serviceName)
    {
        $this->serviceName = $serviceName;
        $this->responseFactory = new ResponseFactory();
    }
}