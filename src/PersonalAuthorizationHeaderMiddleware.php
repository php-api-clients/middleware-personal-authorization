<?php declare(strict_types=1);

namespace ApiClients\Middleware\PersonalAuthorization;

use ApiClients\Foundation\Middleware\ErrorTrait;
use ApiClients\Foundation\Middleware\MiddlewareInterface;
use ApiClients\Foundation\Middleware\PostTrait;
use Psr\Http\Message\RequestInterface;
use React\Promise\CancellablePromiseInterface;
use function React\Promise\resolve;

/**
 * Middleware that adds the authorization header in the token format.
 */
class PersonalAuthorizationHeaderMiddleware implements MiddlewareInterface
{
    use PostTrait;
    use ErrorTrait;

    /**
     * @param  RequestInterface            $request
     * @param  array                       $options
     * @return CancellablePromiseInterface
     */
    public function pre(
        RequestInterface $request,
        string $transactionId,
        array $options = []
    ): CancellablePromiseInterface {
        if (!isset($options[self::class][Options::TOKEN])) {
            return resolve($request);
        }

        if (empty($options[self::class][Options::TOKEN])) {
            return resolve($request);
        }

        return resolve(
            $request->withAddedHeader(
                'Authorization',
                'Personal ' . $options[self::class][Options::TOKEN]
            )
        );
    }
}
