<?php

declare(strict_types=1);

namespace Eglobal\Healthcheck\Checker;

use Aws\S3\S3Client;
use Eglobal\Healthcheck\DTO\Response;

final class S3Check extends AbstractChecker
{
    private string $accessKey;
    private string $secretKey;
    private string $region;
    private string $apiVersion;

    public function __construct(string $accessKey, string $secretKey, string $region, string $apiVersion, string $serviceName)
    {
        parent::__construct($serviceName);

        $this->accessKey = $accessKey;
        $this->secretKey = $secretKey;
        $this->region = $region;
        $this->apiVersion = $apiVersion;
    }

    public function check(): Response
    {
        try {
            $s3Client = new S3Client([
                'credentials' => [
                    'key'    => $this->accessKey,
                    'secret' => $this->secretKey,
                ],
                'region' => $this->region,
                'version' => $this->apiVersion
            ]);

            $buckets = $s3Client->listBuckets()->get('Buckets');
        } catch (\Throwable $exception) {
            return $this->responseFactory->createFailure($this, Response::STATUS_FAIL, "Wrong payload");
        }

        if (null === $buckets || 0 === count($buckets)) {
            return $this->responseFactory->createFailure($this, Response::STATUS_FAIL, "Buckets list is empty");
        }

        return $this->responseFactory->createSuccess($this);
    }

    public function name(): string
    {
        return sprintf('S3 - [%s]', $this->serviceName);
    }

    public function actionDescription(): string
    {
        return 'Checking that buckets list is not empty';
    }

    public function connectionDetails(): array
    {
        return ['message' => 'There is no public connection details'];
    }
}
