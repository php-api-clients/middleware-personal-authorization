<?php declare(strict_types=1);

namespace ApiClients\Tests\Middleware\BearerAuthorization;

use ApiClients\Tools\TestUtilities\TestCase;
use React\EventLoop\Factory;
use RingCentral\Psr7\Request;
use ApiClients\Middleware\BearerAuthorization\BearerAuthorizationHeaderMiddleware;
use ApiClients\Middleware\BearerAuthorization\Options;
use function Clue\React\Block\await;
use function React\Promise\resolve;

final class BearerAuthorizationHeaderMiddlewareTest extends TestCase
{
    public function preProvider()
    {
        yield [
            [],
            false,
            ''
        ];

        yield [
            [
                BearerAuthorizationHeaderMiddleware::class => [
                    Options::TOKEN => '',
                ],
            ],
            false,
            ''
        ];

        yield [
            [
                BearerAuthorizationHeaderMiddleware::class => [
                    Options::TOKEN => null,
                ],
            ],
            false,
            ''
        ];

        yield [
            [
                BearerAuthorizationHeaderMiddleware::class => [
                    Options::TOKEN => 'kroket',
                ],
            ],
            true,
            'Bearer kroket'
        ];
    }

    /**
     * @dataProvider preProvider
     */
    public function testPre(array $options, bool $hasHeader, string $expectedHeader)
    {
        $request = new Request('GET', 'https://example.com/');
        $middleware = new BearerAuthorizationHeaderMiddleware();
        $changedRequest = await($middleware->pre($request, 'abc', $options), Factory::create());

        if ($hasHeader === false) {
            self::assertFalse($changedRequest->hasHeader('Authorization'));
            return;
        }

        self::assertTrue($changedRequest->hasHeader('Authorization'));
        self::assertSame($expectedHeader, $changedRequest->getHeaderLine('Authorization'));
    }
}
