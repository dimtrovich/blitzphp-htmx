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

use BlitzPHP\Http\Request as BaseRequest;

class Request extends BaseRequest
{
    /**
     * Cree une requete htmx a partir de la requete de base
     */
    public static function fromBase(BaseRequest $request): static
    {
        $new = new static();
        $new = $new
            ->withCookieCollection($request->getCookieCollection())
            ->withCookieParams($request->getCookieParams())
            ->withLocale($request->getLocale())
            ->withMethod($request->getMethod())
            ->withParsedBody($request->getParsedBody())
            ->withProtocolVersion($request->getProtocolVersion())
            ->withQueryParams($request->getQueryParams())
            ->withRequestTarget($request->getRequestTarget())
            ->withUploadedFiles($request->getUploadedFiles())
            ->withUri($request->getUri())
            ->withBody($request->getBody());

        foreach ($request->getHeaders() as $name => $value) {
            $new = $new->withHeader($name, $value);
        }

        foreach ($request->getAttributes() as $name => $value) {
            $new = $new->withAttribute($name, $value);
        }

        return $new;
    }

    /**
     * Indicates that the request is triggered by Htmx.
     */
    public function isHtmx(): bool
    {
        return $this->htmxHeaderToBool('HX-Request');
    }

    /**
     * Indicates that the request is via an element using hx-boost.
     */
    public function isBoosted(): bool
    {
        return $this->htmxHeaderToBool('HX-Boosted');
    }

    /**
     * True if the request is for history restoration
     * after a miss in the local history cache.
     */
    public function isHistoryRestoreRequest(): bool
    {
        return $this->htmxHeaderToBool('HX-History-Restore-Request');
    }

    /**
     * The current URL of the browser.
     */
    public function hxCurrentUrl(): ?string
    {
        return $this->htmxHeader('HX-Current-Url');
    }

    /**
     * The user response to an hx-prompt.
     */
    public function hxPrompt(): ?string
    {
        return $this->htmxHeader('HX-Prompt');
    }

    /**
     * The id of the target element if it exists.
     */
    public function hxTarget(): ?string
    {
        return $this->htmxHeader('HX-Target');
    }

    /**
     * The id of the triggered element if it exists.
     */
    public function hxTrigger(): ?string
    {
        return $this->htmxHeader('HX-Trigger');
    }

    /**
     * The name of the triggered element if it exists.
     */
    public function hxTriggerName(): ?string
    {
        return $this->htmxHeader('HX-Trigger-Name');
    }

    /**
     * The value of the header is a JSON serialized
     * version of the event that triggered the request.
     *
     * @see https://htmx.org/extensions/event-header/
     */
    public function hxTriggeringEvent(bool $toArray = true): null|array|object
    {
        if (! $this->hasHeader('Triggering-Event')) {
            return null;
        }

        return json_decode($this->getHeaderLine('Triggering-Event'), $toArray);
    }

    /**
     * Helper method to get the Htmx header value
     */
    private function htmxHeader(string $header): ?string
    {
        if (! str_starts_with($header, 'HX-')) {
            $header = 'HX-' . $header;
        }

        if (! $this->hasHeader($header)) {
            return null;
        }

        return $this->getHeaderLine($header);
    }

    /**
     * Helper method to cast Htmx header to bool
     */
    private function htmxHeaderToBool(string $header): bool
    {
        return $this->htmxHeader($header) === 'true';
    }

    /**
     * Checks this request type.
     *
     * @param string $type HTTP verb or 'json' or 'ajax' or 'htmx' or 'boosted'
     */
    public function is($type, ...$args): bool
    {
        $valueUpper = strtoupper($type);

        if ($valueUpper === 'HTMX') {
            return $this->isHtmx();
        }

        if ($valueUpper === 'BOOSTED') {
            return $this->isBoosted();
        }

        return parent::is($type, ...$args);
    }
}
