<?php

declare(strict_types=1);

namespace Eglobal\Healthcheck\Checker;

use Eglobal\Healthcheck\DTO\Response;
use Predis\Client;
use Snc\RedisBundle\DependencyInjection\Configuration\RedisDsn;

final class RedisCheck extends AbstractChecker
{
    private string $dsn;

    public function __construct(string $dsn, string $serviceName)
    {
        parent::__construct($serviceName);

        $this->dsn = $dsn;
    }

    public function check(): Response
    {
        try {
            $config = new RedisDSN($this->dsn);
            $redis = new Client(
                [
                    'host' => $config->getHost(),
                    'port' => $config->getPort(),
                    'database' => $config->getDatabase(),
                    'password' => $config->getPassword(),
                ]
            );
            $result = $redis->ping();
        } catch (\Throwable $exception) {
            return $this->responseFactory->createFailure($this, Response::STATUS_FAIL, $exception->getMessage());
        }

        if ("PONG" !== $result->getPayload()) {
            return $this->responseFactory->createFailure($this, Response::STATUS_FAIL, sprintf("Wrong payload - %s", $result->getPayload()));
        }

        return $this->responseFactory->createSuccess($this);
    }

    public function name(): string
    {
        return sprintf('Redis - [%s]', $this->serviceName);
    }

    public function actionDescription(): string
    {
        return 'Redis ping action';
    }

    public function connectionDetails(): array
    {
        $data = new RedisDSN($this->dsn);

        return [
            'host' => $data->getHost(),
            'port' => $data->getPort(),
            'database' => $data->getDatabase()
        ];
    }
}
