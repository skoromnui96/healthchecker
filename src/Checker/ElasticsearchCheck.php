<?php

declare(strict_types=1);

namespace Eglobal\Healthcheck\Checker;

use Eglobal\Healthcheck\DTO\Response;
use Eglobal\Healthcheck\Util\DSNParser;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use Symfony\Component\HttpFoundation\Response as ResponseCode;

final class ElasticsearchCheck extends AbstractChecker
{
    private string $dsn;

    public function __construct(string $dsn, string $serviceName)
    {
        parent::__construct($serviceName);

        $this->dsn = $dsn;
    }

    public function check(): Response
    {
        $client = new Client([
            'base_uri' => $this->dsn,
            'connect_timeout' => 3 // seconds
        ]);

        try {
            $response = $client->request('GET', '/_cluster/health');
        } catch (ConnectException $exception) {
            return $this->responseFactory->createFailure($this, Response::STATUS_PENDING, $exception->getMessage());
        } catch (\Throwable $exception) {
            return $this->responseFactory->createFailure($this, Response::STATUS_FAIL, $exception->getMessage());
        }

        if (ResponseCode::HTTP_OK !== $response->getStatusCode()) {
            return $this->responseFactory->createFailure($this, Response::STATUS_FAIL, sprintf('Response code - %s', $response->getStatusCode()));
        }

        return $this->responseFactory->createSuccess($this);
    }

    public function name(): string
    {
        return sprintf('Elasticsearch - [%s]', $this->serviceName);
    }

    public function actionDescription(): string
    {
        return 'Elasticsearch healthcheck request to /_cluster/health';
    }

    public function connectionDetails(): array
    {
        return DSNParser::parse($this->dsn);
    }
}
