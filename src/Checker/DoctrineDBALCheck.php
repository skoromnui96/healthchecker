<?php

declare(strict_types=1);

namespace Eglobal\Healthcheck\Checker;

use Doctrine\DBAL\Exception\ConnectionException;
use Doctrine\DBAL\DriverManager;
use Eglobal\Healthcheck\DTO\Response;
use Eglobal\Healthcheck\Util\DSNParser;

final class DoctrineDBALCheck extends AbstractChecker
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
            $connection = DriverManager::getConnection(['url' => $this->dsn]);
            $query = $connection->getDriver()->getDatabasePlatform()->getDummySelectSQL();

            if (method_exists($connection, 'fetchOne')) {
                $connection->fetchOne($query);
            } else {
                $connection->fetchColumn($query);
            }

            return $this->responseFactory->createSuccess($this);
        } catch (ConnectionException $exception) {
            return $this->responseFactory->createFailure($this, Response::STATUS_PENDING, $exception->getMessage());
        } catch (\Throwable $exception) {
            return $this->responseFactory->createFailure($this, Response::STATUS_FAIL, $exception->getMessage());
        }
    }

    public function name(): string
    {
        return sprintf('Doctrine DBAL - [%s]', $this->serviceName);
    }

    public function actionDescription(): string
    {
        return 'Dummy SQL SELECT';
    }

    public function connectionDetails(): array
    {
        return DSNParser::parse($this->dsn);
    }
}
