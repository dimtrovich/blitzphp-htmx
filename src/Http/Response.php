<?php

/**
 * This file is part of dimtrovich/blitzphp-htmx.
 *
 * (c) 2024 Dimitri Sitchet Tomkeu <devcode.dst@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Dimtrovich\BlitzPHP\Htmx\Http;

use BlitzPHP\Http\Response as BaseResponse;
use InvalidArgumentException;

class Response extends BaseResponse
{
    use HtmxTrait;

    /**
     * Cree une reponse htmx a partir de la reponse de base
     */
    public static function fromBase(BaseResponse $response): static
    {
        $new = new static();
        $new = $new
            ->withCookieCollection($response->getCookieCollection())
            ->withProtocolVersion($response->getProtocolVersion())
            ->withCharset($response->getCharset())
            ->withHeaders($response->getHeaders())
            ->withBody($response->getBody());

        foreach ($response->getCookies() as $name => $value) {
            $new = $new->withCookie(cookie($name, $value));
        }

        return $new;
    }

    /**
     * Pushes a new url into the history stack.
     */
    public function withPushUrl(?string $url = null): static
    {
        return $this->withHeader('HX-Push-Url', $url ?? 'false');
    }

    /**
     * Replaces the current URL in the location bar.
     */
    public function withReplaceUrl(?string $url = null): static
    {
        return $this->withHeader('HX-Replace-Url', $url ?? 'false');
    }

    /**
     * Allows you to specify how the response will be swapped.
     */
    public function withReswap(string $method): static
    {
        $this->validateSwap($method, 'HX-Reswap');

        return $this->withHeader('HX-Reswap', $method);
    }

    /**
     * A CSS selector that updates the target of the content
     * update to a different element on the page.
     */
    public function withRetarget(string $selector): static
    {
        return $this->withHeader('HX-Retarget', $selector);
    }

    /**
     * A CSS selector that allows you to choose which part
     * of the response is used to be swapped in.
     */
    public function withReselect(string $selector): static
    {
        return $this->withHeader('HX-Reselect', $selector);
    }

    /**
     * Allows you to trigger client side events.
     */
    public function triggerClientEvent(string $name, array|string $params = '', string $after = 'receive'): static
    {
        $header = match ($after) {
            'receive' => 'HX-Trigger',
            'settle'  => 'HX-Trigger-After-Settle',
            'swap'    => 'HX-Trigger-After-Swap',
            default   => throw new InvalidArgumentException('A value for "after" argument must be one of: "receive", "settle", or "swap".'),
        };

        if ($this->hasHeader($header)) {
            $data = json_decode($this->getHeaderLine($header), true);
            if ($data === null) {
                throw new InvalidArgumentException(sprintf('%s header value should be a valid JSON.', $header));
            }
            $data[$name] = $params;
        } else {
            $data = [$name => $params];
        }

        return $this->withHeader($header, json_encode($data));
    }
}
