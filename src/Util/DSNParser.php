<?php

declare(strict_types=1);

namespace Eglobal\Healthcheck\Util;

use Eglobal\Healthcheck\Exception\WrongDSNException;

final class DSNParser
{
    public static function parse($dsn): array
    {
        $url = parse_url($dsn);

        if (false === $url) {
            throw WrongDSNException::incorrectDSN($dsn);
        }

        return [
            'scheme' => $url['scheme'] ?? '',
            'host' => $url['host'] ?? '',
            'port' => $url['port'] ?? '',
            'path' => $url['path'] ?? '',
        ];
    }
}
