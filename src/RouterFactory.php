<?php

namespace Nepttune;

use Nette\Application\Routers\RouteList,
    Nette\Application\Routers\Route;

final class RouterFactory
{
    /**
     * @return \Nette\Application\IRouter
     */
    public static function createSubdomainRouter()
    {
        $router = new RouteList();

        $router[] = new Route('//<module>.%domain%/[<locale [a-z]{2}>/]<presenter>/<action>[/<id>]', [
            'presenter' => 'Default',
            'action' => 'default'
        ]);

        return $router;
    }

    /**
     * @return \Nette\Application\IRouter
     */
    public static function createStandardRouter()
    {
        $router = new RouteList();

        $router[] = new Route('/[<locale [a-z]{2}>/]admin/<presenter>/<action>[/<id>]', [
            'module' => 'Admin',
            'presenter' => 'Default',
            'action' => 'default'
        ]);
        
        $router[] = new Route('/[<locale [a-z]{2}>/]<presenter>/<action>[/<id>]', [
            'module' => 'Www',
            'presenter' => 'Default',
            'action' => 'default'
        ]);

        return $router;
    }
}
