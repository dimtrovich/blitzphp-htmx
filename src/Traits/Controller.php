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
