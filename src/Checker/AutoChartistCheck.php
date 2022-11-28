<?php

declare(strict_types=1);

namespace EGlobal\Healthcheck\Checker;

use EGlobal\Healthcheck\DTO\Response;
use EGlobal\Healthcheck\Util\DSNParser;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use Symfony\Component\HttpFoundation\Response as ResponseCode;

final class AutoChartistCheck extends AbstractChecker
{
    private array $urls;
    private string $locale;

    public function __construct(array $urls, string $locale, string $serviceName)
    {
        parent::__construct($serviceName);

        $this->urls = $urls;
        $this->locale = $locale;
    }

    public function check(): Response
    {
        if (!isset($this->urls[$this->locale])) {
            return $this->responseFactory->createFailure($this, Response::STATUS_FAIL, sprintf('Wrong locale [%s]', $this->locale));
        }

        $client = new Client([
            'base_uri' => $this->urls[$this->locale],
            'connect_timeout' => 3 // seconds
        ]);

        try {
            $response = $client->request('GET');
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
        return sprintf('AutoChartist - [%s]', $this->serviceName);
    }

    public function actionDescription(): string
    {
        return 'Verifying that AutoChartist returns analytics';
    }

    public function connectionDetails(): array
    {
        return isset($this->urls[$this->locale]) ? DSNParser::parse($this->urls[$this->locale]) : [];
    }
}
