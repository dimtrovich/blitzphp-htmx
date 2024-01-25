<?php

/**
 * This file is part of dimtrovich/blitzphp-htmx.
 *
 * (c) 2024 Dimitri Sitchet Tomkeu <devcode.dst@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Dimtrovich\BlitzPHP\Htmx\Traits;

use BlitzPHP\Container\Services;
use BlitzPHP\Http\UrlGenerator;
use Dimtrovich\BlitzPHP\Htmx\Http\Redirection;
use Dimtrovich\BlitzPHP\Htmx\Http\Request;
use Dimtrovich\BlitzPHP\Htmx\Http\Response;

/**
 * @property \BlitzPHP\Http\Request  $request
 * @property \BlitzPHP\Http\Response $response
 *
 * @method bool              isHtmx()                                                                                                                                                             Indique que la requete est déclenchée par Htmx.
 * @method bool              isBoosted()                                                                                                                                                          Indique que la requete est faite via un élément utilisant hx-boost.
 * @method bool              isHistoryRestoreRequest()                                                                                                                                            Verifie que la requete concerne la restauration de l'historique après une absence dans le cache de l'historique local.
 * @method ?string           hxCurrentUrl()                                                                                                                                                       L'URL actuelle du navigateur.
 * @method ?string           hxPrompt()                                                                                                                                                           La réponse de l'utilisateur à un hx-prompt.
 * @method ?string           hxTarget()                                                                                                                                                           L'identifiant de l'élément cible s'il existe.
 * @method ?string           hxTrigger()                                                                                                                                                          L'identifiant de l'élément déclenché, s'il existe.
 * @method ?string           hxTriggerName()                                                                                                                                                      Le nom de l'élément déclenché, s'il existe.
 * @method array|object|null hxTriggeringEvent(bool $toArray = true)                                                                                                                              La valeur de l'en-tête est une version sérialisée JSON de l'événement qui a déclenché la requete.
 * @method Response          hxFlash(string $message, string $type = 'success')                                                                                                                   Renvoie une donnée flash dans les headers de la reponse htmx
 * @method Response          hxPushUrl(?string $url = null)                                                                                                                                       Pousse une nouvelle url dans la pile de l'historique.
 * @method Response          hxReplaceUrl(?string $url = null)                                                                                                                                    Remplace l'URL actuelle dans la barre d'adresse.
 * @method Response          hxReswap(string $method)                                                                                                                                             Permet de spécifier comment la réponse sera permutée.
 * @method Response          hxRetarget(string $selector)                                                                                                                                         Sélecteur CSS qui modifie la cible de la mise à jour du contenu en la remplaçant par un autre élément de la page.
 * @method Response          hxReselect(string $selector)                                                                                                                                         Sélecteur CSS qui vous permet de choisir quelle partie de la réponse est utilisée pour être permutée.
 * @method Response          hxTriggerClientEvent(string $name, array|string $params = '', string $after = 'receive')                                                                             Permet de déclencher des événements côté client.
 * @method Redirection       hxRefresh()                                                                                                                                                          Définit le HX-Refresh à true.
 * @method Redirection       hxRedirect(string $uri)                                                                                                                                              Définit l'URI de redirection de HX-Redirect vers lequel rediriger.
 * @method Redirection       hxLocation(string $path, ?string $source = null, ?string $event = null, ?string $target = null, ?string $swap = null, ?array $values = null, ?array $headers = null) Définit l'emplacement HX à rediriger sans recharger la page entière.
 */
trait Controller
{
    protected ?Request $htmxRequest         = null;
    protected ?Response $htmxResponse       = null;
    protected ?Redirection $htmxRedirection = null;

    /**
     * Renvoie l'instance de la requete htmx
     */
    protected function htmxRequest(): Request
    {
        if (null === $this->htmxRequest) {
            $this->htmxRequest = Request::fromBase($this->request);
        }

        return $this->htmxRequest;
    }

    /**
     * Renvoie l'instance de la requete htmx
     */
    protected function htmxResponse(): Response
    {
        if (null === $this->htmxResponse) {
            $this->htmxResponse = Response::fromBase($this->response);
        }

        return $this->htmxResponse;
    }

    /**
     * Renvoie l'instance de la redirection htmx
     */
    protected function htmxRedirection(): Redirection
    {
        if (null === $this->htmxRedirection) {
            $this->htmxRedirection = new Redirection(Services::factory(UrlGenerator::class));
        }

        return $this->htmxRedirection;
    }

    protected function back(int $code = 302, array $headers = [], $fallback = false): Redirection
    {
        return $this->htmxRedirection()->back($code, $headers, $fallback);
    }

    /**
     * @param array|string $key si success vaut true, alors $key est le message de succes.
     *                          sinon c'est l'erreur ou l'ensemble des erreur.
     */
    protected function backHTMX(string $view, array|string $key, bool $success = false)
    {
        if ($success) {
            $this->request->session()->flash('success', $key);
        } else {
            $this->request->session()->flashErrors($key);
        }

        if ($this->isHtmx()) {
            return view($view);
        }

        return $success ? $this->back() : $this->back()->withInput();
    }

    public function __call(string $method, array $args = [])
    {
        if (method_exists($this->htmxRequest(), $method)) {
            return call_user_func_array([$this->htmxRequest(), $method], $args);
        }
        if (method_exists($this->htmxResponse(), $method)) {
            return call_user_func_array([$this->htmxResponse(), $method], $args);
        }
        if (method_exists($this->htmxRedirection(), $method)) {
            return call_user_func_array([$this->htmxRedirection(), $method], $args);
        }

        return parent::__call($method, $args);
    }
}
