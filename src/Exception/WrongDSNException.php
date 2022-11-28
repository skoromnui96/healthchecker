<?php

declare(strict_types=1);

namespace Eglobal\Healthcheck\Exception;

use RuntimeException;

final class WrongDSNException extends RuntimeException
{
    public static function incorrectDSN(string $dsn): self
    {
        return new self(sprintf('DSN: %d is incorrect and can`t be parsed', $dsn));
    }
}
