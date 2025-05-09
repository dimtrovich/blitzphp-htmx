<?php

/**
 * This file is part of dimtrovich/blitzphp-htmx.
 *
 * (c) 2025 Dimitri Sitchet Tomkeu <devcode.dst@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use BlitzPHP\Http\UrlGenerator;
use Dimtrovich\BlitzPHP\Htmx\Http\Redirection;

use function Kahlan\expect;

describe('Http / Redirection', function () {
    beforeEach(function () {
        $this->response = new Redirection(service(UrlGenerator::class));
    });

    describe('HxLocation', function () {
        it('HXLocation fonctionne normalement', function () {
            $this->response = $this->response->hxLocation('foo');

            expect($this->response->hasHeader('HX-Location'))->toBeTruthy();

            $expected = json_encode(['path' => '/foo']);
            expect($this->response->getHeaderLine('HX-Location'))->toBe($expected);
            expect($this->response->getStatusCode())->toBe(200);
        });

        it('HxLocation avec une Url complete', function () {
            $this->response = $this->response->hxLocation('https://example.com/foo1');

            expect($this->response->getHeaderLine('HX-Location'))->toBe(json_encode(['path' => '/foo1']));

            $this->response = $this->response->hxLocation('http://example.com/foo2');

            expect($this->response->getHeaderLine('HX-Location'))->toBe(json_encode(['path' => '/foo2']));

            $this->response = $this->response->hxLocation('http://example.com/foo3?page=1&sort=ASC#top');

            expect($this->response->getHeaderLine('HX-Location'))->toBe(json_encode(['path' => '/foo3?page=1&sort=ASC#top']));
        });

        it('HxLocation avec la source et l\'evenement', function () {
            $this->response = $this->response->hxLocation(path: '/foo', source: '#myElem', event: 'doubleclick');

            expect($this->response->hasHeader('HX-Location'))->toBeTruthy();
            $expected = json_encode(['path' => '/foo', 'source' => '#myElem', 'event' => 'doubleclick']);
            expect($expected)->toBe($this->response->getHeaderLine('HX-Location'));
            expect(200)->toBe($this->response->getStatusCode());
        });

        it('HxLocation avec la cible et le swap', function () {
            $this->response = $this->response->hxLocation(path: '/foo', target: '#myDiv', swap: 'outerHTML');

            expect($this->response->hasHeader('HX-Location'))->toBeTruthy();
            $expected = json_encode(['path' => '/foo', 'target' => '#myDiv', 'swap' => 'outerHTML']);
            expect($expected)->toBe($this->response->getHeaderLine('HX-Location'));
            expect(200)->toBe($this->response->getStatusCode());
        });

        it('HxLocation avec les valeurs et entetes', function () {
            $this->response = $this->response->hxLocation(path: '/foo', values: ['myVal' => 'My Value'], headers: ['myHeader' => 'My Value']);

            expect($this->response->hasHeader('HX-Location'))->toBeTruthy();
            $expected = json_encode(['path' => '/foo', 'values' => ['myVal' => 'My Value'], 'headers' => ['myHeader' => 'My Value']]);
            expect($expected)->toBe($this->response->getHeaderLine('HX-Location'));
            expect(200)->toBe($this->response->getStatusCode());
        });
        
        it('HxLocation avec le selecteur', function () {
            $this->response = $this->response->hxLocation(path: '/foo', select: '#hx-container > *');

            expect($this->response->hasHeader('HX-Location'))->toBeTruthy();
            $expected = json_encode(['path' => '/foo', 'select' => '#hx-container > *']);
            expect($expected)->toBe($this->response->getHeaderLine('HX-Location'));
            expect(200)->toBe($this->response->getStatusCode());
        });

        it('HxLocation leve une exception en cas d\'arguments non valide', function () {
            expect(fn () => $this->response = $this->response->hxLocation(path: '/foo', swap: 'foo'))
                ->toThrow(new InvalidArgumentException());
        });
    });
});
