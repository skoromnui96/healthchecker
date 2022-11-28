<?php

declare(strict_types=1);

namespace Eglobal\Healthcheck\Service;

use Eglobal\Healthcheck\Factory\CheckerFactory;

class HealthCheckService
{
    private CheckerFactory $checkerFactory;

    public function __construct(CheckerFactory $checkerFactory)
    {
        $this->checkerFactory = $checkerFactory;
    }

    public function check(array $checkersList, bool $stopOnFailure): array
    {
        $result = [];
        $checkers = $this->checkerFactory->createFromList($checkersList);

        foreach ($checkers as $checker) {
            $response = $checker->check();
            $result[] = $response->toArray();

            if (true === $stopOnFailure && $response->fail()) {
                return $result;
            }
        }

        return $result;
    }
}
