<?php

namespace Nepttune;

use Nette\Application\Routers\RouteList,
    Nette\Application\Routers\Route;

class RouterFactory
{
    protected static function createRouteList() : RouteList
    {
        $router = new RouteList();

        $router[] = new Route('/robots.txt', 'Tool:robots');
        $router[] = new Route('/sitemap.xml', 'Tool:sitemap');
        $router[] = new Route('/worker.js', 'Tool:worker');

        return $router;
    }

    public static function createSubdomainRouter() : RouteList
    {
        $router = static::createRouteList();

        $router[] = new Route('//<module>.%domain%/[<locale>/]<presenter>/<action>[/<id>]', [
            'locale' => [Route::PATTERN => '[a-z]{2}'],
            'presenter' => 'Default',
            'action' => 'default',
            'id' => [Route::PATTERN => '\d+']
        ]);

        return $router;
    }

    public static function createStandardRouter() : RouteList
    {
        $router = static::createRouteList();
        
        $router[] = new Route('/api/<presenter>/<action>', [
            'module' => 'Api',
            'presenter' => 'Default',
            'action' => 'default'
        ]);
        
        $router[] = new Route('/[<locale>/]<presenter>/<action>[/<id>]', [
            'locale' => [Route::PATTERN => '[a-z]{2}'],
            'module' => 'Www',
            'presenter' => 'Default',
            'action' => 'default',
            'id' => [Route::PATTERN => '\d+']
        ]);

        return $router;
    }
}
