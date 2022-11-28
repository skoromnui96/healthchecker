<?php

declare(strict_types=1);

namespace Eglobal\Healthcheck\DTO;

final class Response
{
    public const STATUS_SUCCESS = 'success';
    public const STATUS_PENDING = 'pending';
    public const STATUS_FAIL = 'fail';

    private string $name;
    private array $connectionDetails;
    private string $status;
    private string $message;
    private int $checkTimestamp;

    public function __construct(string $name, array $connectionDetails, string $status, string $message)
    {
        $this->name = $name;
        $this->connectionDetails = $connectionDetails;
        $this->status = $status;
        $this->message = $message;
        $this->checkTimestamp = time();
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'connection_details' => $this->connectionDetails,
            'status' => $this->status,
            'message' => $this->message,
            'check_timestamp' => $this->checkTimestamp,
        ];
    }

    public function success(): bool
    {
        return self::STATUS_SUCCESS === $this->status;
    }

    public function fail(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_FAIL], true);
    }
}
