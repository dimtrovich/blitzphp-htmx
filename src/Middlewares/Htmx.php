<?php

/**
 * This file is part of dimtrovich/blitzphp-htmx.
 *
 * (c) 2024 Dimitri Sitchet Tomkeu <devcode.dst@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Dimtrovich\BlitzPHP\Htmx\Middlewares;

use BlitzPHP\Middlewares\BaseMiddleware;
use BlitzPHP\View\View;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Htmx extends BaseMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        View::share([
            'isHTMXRequest' => $request->hasHeader('Hx-Request'),
            'isHTMXBoosted' => $request->hasHeader('Hx-Boosted'),
        ]);

        $request = $request
            ->withAttribute('htmx', $request->hasHeader('Hx-Request'))
            ->withAttribute('htmx-boosted', $request->hasHeader('Hx-Boosted'))
            ->withAttribute('htmx-trigger', ! $request->hasHeader('Hx-Boosted') && ! $request->hasHeader('Hx-Request'));

        return $handler->handle($request);
    }
}
