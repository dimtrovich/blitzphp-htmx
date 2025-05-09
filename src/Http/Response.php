<?php

/**
 * This file is part of dimtrovich/blitzphp-htmx.
 *
 * (c) 2025 Dimitri Sitchet Tomkeu <devcode.dst@gmail.com>
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
     *
     * @internal
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
     * Renvoie une donnée flash dans les headers de la reponse htmx
     *
     * Ceci permet au frontend de recuperer et potentiellement afficher un popup du genre sweet alert
     */
    public function hxFlash(string $message, string $type = 'success'): static
    {
        return $this->withHeader('BHX-Flash', json_encode(compact('message', 'type')));
    }

    /**
     * Pousse une nouvelle url dans la pile de l'historique.
     */
    public function hxPushUrl(?string $url = null): static
    {
        return $this->withHeader('HX-Push-Url', $url ?? 'false');
    }

    /**
     * Remplace l'URL actuelle dans la barre d'adresse.
     */
    public function hxReplaceUrl(?string $url = null): static
    {
        return $this->withHeader('HX-Replace-Url', $url ?? 'false');
    }

    /**
     * Permet de spécifier comment la réponse sera permutée.
     */
    public function hxReswap(string $method): static
    {
        $this->validateSwap($method, 'HX-Reswap');

        return $this->withHeader('HX-Reswap', $method);
    }

    /**
     * Sélecteur CSS qui modifie la cible de la mise à jour du contenu en la remplaçant par un autre élément de la page.
     */
    public function hxRetarget(string $selector): static
    {
        return $this->withHeader('HX-Retarget', $selector);
    }

    /**
     * Sélecteur CSS qui vous permet de choisir quelle partie de la réponse est utilisée pour être permutée.
     */
    public function hxReselect(string $selector): static
    {
        return $this->withHeader('HX-Reselect', $selector);
    }

    /**
     * Permet de déclencher des événements côté client.
     */
    public function hxTriggerClientEvent(string $name, array|string $params = '', string $after = 'receive'): static
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
