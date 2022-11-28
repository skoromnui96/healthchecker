<?php

declare(strict_types=1);

namespace Tests\EGlobal\Healthcheck\tests\Util;

use EGlobal\Healthcheck\Util\DSNParser;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \EGlobal\Healthcheck\Util\DSNParser
 */
final class DSNParserTest extends TestCase
{
    /**
     * @test
     *
     * @covers ::parse
     *
     * @dataProvider dsnProvider
     */
    public function it_returns_all_checkers_on_empty_input_list(string $dsn, array $expectedResult): void
    {
        $result = DSNParser::parse($dsn);

        $this->assertEquals($expectedResult, $result);
    }

    public function dsnProvider(): \Traversable
    {
        yield [
            'https://test.com:88/check',
            [
                'scheme' => 'https',
                'host' => 'test.com',
                'port' => '88',
                'path' => '/check'
            ]
        ];
        yield [
            'mysql://accdev:siski@10.5.2.29:3306/account?serverVersion=5.7.36',
            [
                'scheme' => 'mysql',
                'host' => '10.5.2.29',
                'port' => '3306',
                'path' => '/account'
            ]
        ];
        yield [
            'redis://redis:6379/1',
            [
                'scheme' => 'redis',
                'host' => 'redis',
                'port' => '6379',
                'path' => '/1'
            ]
        ];
    }
}
