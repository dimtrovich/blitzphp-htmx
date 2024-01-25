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

use BlitzPHP\Contracts\Http\StatusCode;
use BlitzPHP\Http\Redirection as BaseRedirection;

class Redirection extends BaseRedirection
{
    use HtmxTrait;

    /**
     * Définit l'emplacement HX à rediriger sans recharger la page entière.
     */
    public function hxLocation(
        string $path,
        ?string $source = null,
        ?string $event = null,
        ?string $target = null,
        ?string $swap = null,
        ?array $values = null,
        ?array $headers = null
    ): static {
        $data = ['path' => '/' . ltrim($path, '/')];

        if ($source !== null) {
            $data['source'] = $source;
        }

        if ($event !== null) {
            $data['event'] = $event;
        }

        if ($target !== null) {
            $data['target'] = $target;
        }

        if ($swap !== null) {
            $this->validateSwap($swap);
            $data['swap'] = $swap;
        }

        if (! empty($values)) {
            $data['values'] = $values;
        }

        if (! empty($headers)) {
            $data['headers'] = $headers;
        }

        return $this->withStatus(StatusCode::OK)->withHeader('HX-Location', json_encode($data));
    }

    /**
     * Définit l'URI de redirection de HX-Redirect vers lequel rediriger.
     *
     * @param string $uri The URI to redirect to
     */
    public function hxRedirect(string $uri): static
    {
        if (! str_starts_with($uri, 'http')) {
            $uri = site_url($uri);
        }

        return $this->withStatus(StatusCode::OK)->withHeader('HX-Redirect', $uri);
    }

    /**
     * Définit le HX-Refresh à true.
     */
    public function hxRefresh(): static
    {
        return $this->withStatus(StatusCode::OK)->withHeader('HX-Refresh', 'true');
    }
}
