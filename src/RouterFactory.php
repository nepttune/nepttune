<?php

/**
 * This file is part of Nepttune (https://www.peldax.com)
 *
 * Copyright (c) 2018 Václav Pelíšek (info@peldax.com)
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license. For more information, see
 * <https://www.peldax.com>.
 */

declare(strict_types = 1);

namespace Nepttune;

use Nette\Application\Routers\RouteList,
    Nette\Application\Routers\Route;

class RouterFactory
{
    /** @var array */
    protected $config;

    public function injectConfig(array $config) : void
    {
        $this->config = $config;
    }

    public function createSubdomainRouter() : RouteList
    {
        $router = static::createRouteList();
        $router = $this->addSubdomainRoutes($router);
        return $router;
    }

    public function createStandardRouter() : RouteList
    {
        $router = static::createRouteList();
        $router = $this->addStandardRoutes($router);
        return $router;
    }

    protected static function createRouteList() : RouteList
    {
        $router = new RouteList();

        $router[] = new Route('/robots.txt', 'Tool:robots');
        $router[] = new Route('/sitemap.xml', 'Tool:sitemap');
        $router[] = new Route('/worker.js', 'Tool:worker');
        $router[] = new Route('/manifest.json', 'Tool:manifest');
        $router[] = new Route('/browserconfig.xml', 'Tool:browserconfig');
        $router[] = new Route('/security.txt', 'Tool:security');
        $router[] = new Route('/.well-known/security.txt', 'Tool:security');
        $router[] = new Route('/push-subscribe', 'Tool:subscribe');

        return $router;
    }

    protected function addSubdomainRoutes(RouteList $router) : RouteList
    {
        $router[] = new Route('//<module>.%domain%/[<locale>/]<presenter>/<action>[/<id>]', [
            'locale' => [Route::PATTERN => '[a-z]{2}'],
            'presenter' => 'Default',
            'action' => 'default',
            'id' => $this->getIdConfig(),
            null => $this->getGlobalConfig()
        ]);
        
        return $router;
    }

    protected function addStandardRoutes(RouteList $router) : RouteList
    {
        $router[] = new Route('/api/<presenter>/<action>', [
            'module' => 'Api',
            'presenter' => 'Default',
            'action' => 'default'
        ]);

        $router[] = new Route('/[<locale>/]<presenter>/<action>[/<id>]', [
            'locale' => [Route::PATTERN => '[a-z]{2}'],
            'module' => $this->config['defaultModule'],
            'presenter' => 'Default',
            'action' => 'default',
            'id' => $this->getIdConfig(),
            null => $this->getGlobalConfig()
        ]);

        return $router;
    }

    protected function getIdConfig() : array
    {
        if ($this->config['hashids'])
        {
            return [];
        }

        return [Route::PATTERN => '\d+'];
    }

    protected function getGlobalConfig() : array
    {
        if ($this->config['hashids'])
        {
            return [Route::FILTER_IN => [$this, 'filterIn'], Route::FILTER_OUT => [$this, 'filterOut']];
        }

        return [];
    }

    protected function getHashIds(array $parameters) : \Hashids\Hashids
    {
        $dest = "{$parameters['module']}:{$parameters['presenter']}:{$parameters['action']}";

        return new \Hashids\Hashids(
            $this->config['hashidsSalt'] . $dest,
            $this->config['hashidsPadding'],
            $this->config['hashidsCharset']
        );
    }

    public function filterIn(array $parameters) : array
    {
        if (!empty($parameters['id']))
        {
            $hashIds = $this->getHashIds($parameters);
            $parameters['id'] = (int) $hashIds->decode($parameters['id'])[0];
        }

        return $parameters;
    }

    public function filterOut(array $parameters) : array
    {
        if (!empty($parameters['id']))
        {
            $hashIds = $this->getHashIds($parameters);
            $parameters['id'] = (string) $hashIds->encode($parameters['id']);
        }

        return $parameters;
    }
}
