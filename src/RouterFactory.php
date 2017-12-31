<?php

namespace Peldax\NetteInit;

use Nette\Application\Routers\RouteList,
    Nette\Application\Routers\Route;

final class RouterFactory
{
    /**
     * @return \Nette\Application\IRouter
     */
    public static function createRouter()
    {
        $router = new RouteList();

        $router[] = new Route('//<module>.%domain%/[<lang [a-z]{2}>/]<presenter>/<action>[/<id>]', [
            'presenter' => 'Default',
            'action' => 'default'
        ]);

        return $router;
    }
}
