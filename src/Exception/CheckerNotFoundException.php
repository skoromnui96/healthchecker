<?php

declare(strict_types=1);

namespace Eglobal\Healthcheck\Exception;

use RuntimeException;

final class CheckerNotFoundException extends RuntimeException
{
    public static function createByServiceName(string $name): self
    {
        return new self(sprintf('Service name: %d is incorrect', $name));
    }
}
