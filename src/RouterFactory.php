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
    const DEFAULT_MODULE = 'Www';
    
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

    protected static function addSubdomainRoutes(RouteList $router) : RouteList
    {
        $router[] = new Route('//<module>.%domain%/[<locale>/]<presenter>/<action>[/<id>]', [
            'locale' => [Route::PATTERN => '[a-z]{2}'],
            'presenter' => 'Default',
            'action' => 'default',
            'id' => [Route::PATTERN => '\d+']
        ]);
        
        return $router;
    }

    protected static function addStandardRoutes(RouteList $router, string $defaultModule = null) : RouteList
    {
        $router[] = new Route('/api/<presenter>/<action>', [
            'module' => 'Api',
            'presenter' => 'Default',
            'action' => 'default'
        ]);

        $router[] = new Route('/[<locale>/]<presenter>/<action>[/<id>]', [
            'locale' => [Route::PATTERN => '[a-z]{2}'],
            'module' => $defaultModule ?: static::DEFAULT_MODULE,
            'presenter' => 'Default',
            'action' => 'default',
            'id' => [Route::PATTERN => '\d+']
        ]);

        return $router;
    }

    public static function createSubdomainRouter() : RouteList
    {
        $router = static::createRouteList();
        $router = static::addSubdomainRoutes($router);
        return $router;
    }

    public static function createStandardRouter(string $defaultModule = null) : RouteList
    {
        $router = static::createRouteList();
        $router = static::addStandardRoutes($router, $defaultModule);
        return $router;
    }
}

