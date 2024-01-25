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
     *
     * @internal
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
     * Indique que la requete est déclenchée par Htmx.
     */
    public function isHtmx(): bool
    {
        return $this->htmxHeaderToBool('HX-Request');
    }

    /**
     * Indique que la requete est faite via un élément utilisant hx-boost.
     */
    public function isBoosted(): bool
    {
        return $this->htmxHeaderToBool('HX-Boosted');
    }

    /**
     * Verifie que la requete concerne la restauration de l'historique après une absence dans le cache de l'historique local.
     */
    public function isHistoryRestoreRequest(): bool
    {
        return $this->htmxHeaderToBool('HX-History-Restore-Request');
    }

    /**
     * L'URL actuelle du navigateur.
     */
    public function hxCurrentUrl(): ?string
    {
        return $this->htmxHeader('HX-Current-Url');
    }

    /**
     * La réponse de l'utilisateur à un hx-prompt.
     */
    public function hxPrompt(): ?string
    {
        return $this->htmxHeader('HX-Prompt');
    }

    /**
     * L'identifiant de l'élément cible s'il existe.
     */
    public function hxTarget(): ?string
    {
        return $this->htmxHeader('HX-Target');
    }

    /**
     * L'identifiant de l'élément déclenché, s'il existe.
     */
    public function hxTrigger(): ?string
    {
        return $this->htmxHeader('HX-Trigger');
    }

    /**
     * Le nom de l'élément déclenché, s'il existe.
     */
    public function hxTriggerName(): ?string
    {
        return $this->htmxHeader('HX-Trigger-Name');
    }

    /**
     * La valeur de l'en-tête est une version sérialisée JSON de l'événement qui a déclenché la requete.
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
     * Méthode d'aide pour obtenir la valeur de l'en-tête Htmx
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
     * Méthode d'aide pour convertir l'en-tête Htmx en bool
     */
    private function htmxHeaderToBool(string $header): bool
    {
        return $this->htmxHeader($header) === 'true';
    }

    /**
     * {@inheritDoc}
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
