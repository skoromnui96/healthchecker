<?php

declare(strict_types=1);

namespace Eglobal\Healthcheck\Factory;

use Eglobal\Healthcheck\Checker\CheckerInterface;
use Eglobal\Healthcheck\DTO\Response;

final class ResponseFactory
{
    public function createSuccess(CheckerInterface $checker): Response
    {
        $message =  sprintf('%s - [Success]', $checker->actionDescription());

        return $this->createResponse($checker, Response::STATUS_SUCCESS, $message);
    }

    public function createFailure(CheckerInterface $checker, string $status, string $errorMessage): Response
    {
        $message =  sprintf('%s. Finished with error - %s', $checker->actionDescription(), $errorMessage);

        return $this->createResponse($checker, $status, $message);
    }

    private function createResponse(CheckerInterface $checker, string $status, string $message): Response
    {
        return new Response($checker->name(), $checker->connectionDetails(), $status, $message);
    }
}
