<?php

declare(strict_types=1);

namespace EGlobal\Healthcheck\Checker;

use Eglobal\Healthcheck\DTO\Response;

interface CheckerInterface
{
    public function check(): Response;
    public function name(): string;
    public function actionDescription(): string;
    public function connectionDetails(): array;
}
