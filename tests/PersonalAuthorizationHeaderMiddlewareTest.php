<?php declare(strict_types=1);

namespace ApiClients\Tests\Middleware\PersonalAuthorization;

use ApiClients\Middleware\PersonalAuthorization\Options;
use ApiClients\Middleware\PersonalAuthorization\PersonalAuthorizationHeaderMiddleware;
use ApiClients\Tools\TestUtilities\TestCase;
use function Clue\React\Block\await;
use React\EventLoop\Factory;
use RingCentral\Psr7\Request;

/**
 * @internal
 */
final class PersonalAuthorizationHeaderMiddlewareTest extends TestCase
{
    public function preProvider()
    {
        yield [
            [],
            false,
            '',
        ];

        yield [
            [
                PersonalAuthorizationHeaderMiddleware::class => [
                    Options::TOKEN => '',
                ],
            ],
            false,
            '',
        ];

        yield [
            [
                PersonalAuthorizationHeaderMiddleware::class => [
                    Options::TOKEN => null,
                ],
            ],
            false,
            '',
        ];

        yield [
            [
                PersonalAuthorizationHeaderMiddleware::class => [
                    Options::TOKEN => 'kroket',
                ],
            ],
            true,
            'Personal kroket',
        ];
    }

    /**
     * @dataProvider preProvider
     */
    public function testPre(array $options, bool $hasHeader, string $expectedHeader): void
    {
        $request = new Request('GET', 'https://example.com/');
        $middleware = new PersonalAuthorizationHeaderMiddleware();
        $changedRequest = await($middleware->pre($request, 'abc', $options), Factory::create());

        if ($hasHeader === false) {
            self::assertFalse($changedRequest->hasHeader('Authorization'));

            return;
        }

        self::assertTrue($changedRequest->hasHeader('Authorization'));
        self::assertSame($expectedHeader, $changedRequest->getHeaderLine('Authorization'));
    }
}
