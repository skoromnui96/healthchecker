<?php

declare(strict_types=1);

namespace EGlobal\HealthCheck\Checker;

use EGlobal\Healthcheck\DTO\Response;
use EGlobal\Healthcheck\Util\DSNParser;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Request;
use Symfony\Component\HttpFoundation\Response as ResponseCode;

final class ApiRequestCheck extends AbstractChecker
{
    private string $baseUrl;
    private string $endpoint;
    private string $requestMethod;
    private array $body;
    private array $headers;
    private bool $requestBodyInPath;

    public function __construct(
        string $baseUrl,
        string $endpoint,
        string $requestMethod,
        array $body,
        array $headers,
        bool $requestBodyInPath,
        string $serviceName
    )
    {
        parent::__construct($serviceName);

        $this->baseUrl = $baseUrl;
        $this->endpoint = $endpoint;
        $this->requestMethod = $requestMethod;
        $this->body = $body;
        $this->headers = $headers;
        $this->requestBodyInPath = $requestBodyInPath;
    }

    public function check(): Response
    {
        $client = new Client([
            'base_uri' => $this->baseUrl,
            'connect_timeout' => 3, // seconds
        ]);

        if ($this->requestBodyInPath) {
            $body = urldecode(http_build_query($this->body));
        } else {
            $body = json_encode($this->body);
        }

        $request =  new Request($this->requestMethod, $this->endpoint, $this->headers, $body);

        try {
            $response = $client->send($request);
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
        return sprintf('Api Request - [%s]', $this->serviceName);
    }

    public function actionDescription(): string
    {
        return 'Api request using Guzzle';
    }

    public function connectionDetails(): array
    {
        return DSNParser::parse($this->baseUrl . $this->endpoint);
    }
}
