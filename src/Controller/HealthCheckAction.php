<?php

declare(strict_types=1);

namespace Eglobal\Healthcheck\Controller;

use Eglobal\Healthcheck\Service\HealthCheckService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(
 *     methods={"GET"},
 *     path="/healthcheck",
 *     name="fx.health_check"
 * )
 */
final class HealthCheckAction
{
    private HealthCheckService $healthCheckService;

    public function __construct(HealthCheckService $healthCheckService)
    {
        $this->healthCheckService = $healthCheckService;
    }

    public function __invoke(Request $request): JsonResponse
    {
        $services = $this->services($request->get('services'));
        $stopOnFailure = filter_var($request->get('stop_on_failure'), FILTER_VALIDATE_BOOLEAN);

        $response = $this->healthCheckService->check($services, $stopOnFailure);

        return new JsonResponse($response);
    }

    private function services(?string $services): array
    {
        if (null === $services || '' === $services) {
            return [];
        }

        return explode(',', $services);
    }
}
