<?php

declare(strict_types=1);

namespace Eglobal\Healthcheck\DependencyInjection;

use Eglobal\Healthcheck\Checker\CheckerInterface;

final class CheckersContainer
{
    private array $checkers;

    public function __construct()
    {
        $this->checkers = [];
    }

    public function add(string $alias, CheckerInterface $checker): void
    {
        $this->checkers[$alias] = $checker;
    }

    public function has($alias): bool
    {
        return array_key_exists($alias, $this->checkers);
    }

    public function get($alias): ?CheckerInterface
    {
        if (array_key_exists($alias, $this->checkers)) {
            return $this->checkers[$alias];
        }

        return null;
    }

    public function all(): array
    {
        return array_values($this->checkers);
    }
}
