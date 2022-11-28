<?php

declare(strict_types=1);

namespace Tests\EGlobal\Healthcheck\tests\Factory;

use EGlobal\Healthcheck\Checker\CheckerInterface;
use EGlobal\Healthcheck\DependencyInjection\CheckersContainer;
use EGlobal\Healthcheck\Exception\CheckerNotFoundException;
use EGlobal\Healthcheck\Factory\CheckerFactory;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \EGlobal\Healthcheck\Factory\CheckerFactory
 */
final class CheckerFactoryTest extends TestCase
{
    private CheckerFactory $checkerFactory;

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        $checkersContainer = new CheckersContainer();
        $checkersContainer->add('test', $this->createCheckerMock('test'));
        $checkersContainer->add('db', $this->createCheckerMock('db'));
        $checkersContainer->add('redis', $this->createCheckerMock('redis'));

        $this->checkerFactory = new CheckerFactory($checkersContainer);
    }

    /**
     * @test
     *
     * @covers ::createFromList
     */
    public function it_returns_correct_checker(): void
    {
        $checkers = $this->checkerFactory->createFromList(['db']);

        $this->assertCount(1, $checkers);
        $this->assertEquals('db', $checkers[0]->name());
    }

    /**
     * @test
     *
     * @covers ::createFromList
     */
    public function it_returns_all_checkers_on_empty_input_list(): void
    {
        $checkers = $this->checkerFactory->createFromList([]);

        $this->assertCount(3, $checkers);
    }

    /**
     * @test
     */
    public function it_returns_exception_on_wrong_service_name_passed(): void
    {
        $this->expectException(CheckerNotFoundException::class);

        $this->checkerFactory->createFromList(['elastic']);
    }

    private function createCheckerMock(string $alias): CheckerInterface
    {
        $checker = $this->createMock(CheckerInterface::class);
        $checker->method('name')->willReturn($alias);

        return $checker;
    }
}
