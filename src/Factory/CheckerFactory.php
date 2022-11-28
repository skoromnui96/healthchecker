<?php

declare(strict_types=1);

namespace Eglobal\Healthcheck\Factory;

use Eglobal\Healthcheck\Checker\CheckerInterface;
use Eglobal\Healthcheck\DependencyInjection\CheckersContainer;
use Eglobal\Healthcheck\Exception\CheckerNotFoundException;

final class CheckerFactory
{
    private CheckersContainer $checkersContainer;

    public function __construct(CheckersContainer $checkersContainer)
    {
        $this->checkersContainer = $checkersContainer;
    }

    /**
     * @return CheckerInterface[]
     */
    public function createFromList(array $services): array
    {
        if (empty($services)) {
            return $this->checkersContainer->all();
        }

        $checkers = [];
        foreach ($services as $service) {
            $checkers[] = $this->create($service);
        }

        return $checkers;
    }

    private function create(string $service): CheckerInterface
    {
        if (!$this->checkersContainer->has($service)) {
            throw CheckerNotFoundException::createByServiceName($service);
        }

        return $this->checkersContainer->get($service);
    }
}
