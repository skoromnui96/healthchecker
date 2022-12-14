<?php

declare(strict_types=1);

namespace Tests\EGlobal\Healthcheck\tests\Service;

use EGlobal\Healthcheck\Checker\CheckerInterface;
use EGlobal\Healthcheck\DependencyInjection\CheckersContainer;
use EGlobal\Healthcheck\DTO\Response;
use EGlobal\Healthcheck\Factory\CheckerFactory;
use EGlobal\Healthcheck\Service\HealthCheckService;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \EGlobal\Healthcheck\Service\HealthCheckService
 */
class HealthCheckServiceTest extends TestCase
{
    private HealthCheckService $healthCheckService;

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        $checkersContainer = new CheckersContainer();
        $checkersContainer->add('test', $this->createSuccessCheckerMock('test'));
        $checkersContainer->add('db', $this->createFailureCheckerMock('db'));
        $checkersContainer->add('redis', $this->createSuccessCheckerMock('redis'));
        $checkersContainer->add('elastic', $this->createSuccessCheckerMock('elastic'));

        $checkerFactory = new CheckerFactory($checkersContainer);
        $this->healthCheckService = new HealthCheckService($checkerFactory);
    }

    /**
     * @test
     *
     * @covers ::check
     */
    public function it_handle_all_checks_ignoring_failures()
    {
        $response = $this->healthCheckService->check(['test', 'db', 'redis', 'elastic'], false);

        $this->assertCount(4, $response);
        $this->assertServiceResponseExist($response, 'test', Response::STATUS_SUCCESS);
        $this->assertServiceResponseExist($response, 'db', Response::STATUS_FAIL);
        $this->assertServiceResponseExist($response, 'redis', Response::STATUS_SUCCESS);
        $this->assertServiceResponseExist($response, 'elastic', Response::STATUS_SUCCESS);
    }

    /**
     * @test
     *
     * @covers ::check
     */
    public function it_stop_checks_on_first_failure()
    {
        $response = $this->healthCheckService->check(['test', 'db', 'redis', 'elastic'], true);

        $this->assertCount(2, $response);
        $this->assertServiceResponseExist($response, 'test', Response::STATUS_SUCCESS);
        $this->assertServiceResponseExist($response, 'db', Response::STATUS_FAIL);
    }

    private function createSuccessCheckerMock(string $alias): CheckerInterface
    {
        $checker = $this->createMock(CheckerInterface::class);
        $checker->method('name')->willReturn($alias);
        $checker->method('check')->willReturn(
            new Response($alias, [], Response::STATUS_SUCCESS, 'success')
        );

        return $checker;
    }

    private function createFailureCheckerMock(string $alias): CheckerInterface
    {
        $checker = $this->createMock(CheckerInterface::class);
        $checker->method('name')->willReturn($alias);
        $checker->method('check')->willReturn(
            new Response($alias, [], Response::STATUS_FAIL, 'error')
        );

        return $checker;
    }

    private function assertServiceResponseExist(array $response, string $serviceName, string $expectedStatus): void
    {
        $responseKey = array_search($serviceName, array_column($response, 'name'));

        $this->assertEquals($expectedStatus, $response[$responseKey]['status']);
    }
}
