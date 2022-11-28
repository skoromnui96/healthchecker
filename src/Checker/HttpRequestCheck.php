<?php

declare(strict_types=1);

namespace Eglobal\Healthcheck\Checker;

use Eglobal\Healthcheck\DTO\Response;
use Eglobal\Healthcheck\Util\DSNParser;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use Symfony\Component\HttpFoundation\Response as ResponseCode;

final class HttpRequestCheck extends AbstractChecker
{
    private string $url;
    private string $requestMethod;

    public function __construct(string $url, string $requestMethod, string $serviceName)
    {
        parent::__construct($serviceName);
        $this->url = $url;
        $this->requestMethod = $requestMethod;
    }

    public function check(): Response
    {
        $client = new Client([
            'base_uri' => $this->url,
            'connect_timeout' => 3 // seconds
        ]);

        try {
            $response = $client->request($this->requestMethod);
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
        return sprintf('Http Request - [%s]', $this->serviceName);
    }

    public function actionDescription(): string
    {
        return 'Simple Http Request';
    }

    public function connectionDetails(): array
    {
        return DSNParser::parse($this->url);
    }
}
