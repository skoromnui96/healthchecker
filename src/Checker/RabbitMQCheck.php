<?php

declare(strict_types=1);

namespace Eglobal\Healthcheck\Checker;

use Eglobal\Healthcheck\DTO\Response;
use PhpAmqpLib\Connection\AMQPSocketConnection;

final class RabbitMQCheck extends AbstractChecker
{
    private string $host;
    private int $port;
    private string $user;
    private string $password;
    private string $vhost;

    public function __construct(
        string $host,
        int $port,
        string $user,
        string $password,
        string $vhost,
        string $serviceName
    ) {
        parent::__construct($serviceName);

        $this->host = $host;
        $this->port = $port;
        $this->user = $user;
        $this->password = $password;
        $this->vhost = $vhost;
    }

    public function check(): Response
    {
        try {
            $client = new AMQPSocketConnection($this->host, $this->port, $this->user, $this->password, $this->vhost);
            $client->checkHeartBeat();
        } catch (\Throwable $exception) {
            return $this->responseFactory->createFailure($this, Response::STATUS_FAIL, $exception->getMessage());
        }

        return $this->responseFactory->createSuccess($this);
    }

    public function name(): string
    {
        return sprintf('RabbitMQ - [%s]', $this->serviceName);
    }

    public function actionDescription(): string
    {
        return 'RabbitMQ heart beat action';
    }

    public function connectionDetails(): array
    {
        return [
            'schema' => 'amqp',
            'host' => $this->host,
            'port' => $this->port,
            'path' => $this->vhost
        ];
    }
}
