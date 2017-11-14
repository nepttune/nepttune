<?php

namespace Peldax\NetteInit\Component;

class Menu extends BaseComponent
{
    /** @var array */
    protected $menu;

    public function __construct(array $menu)
    {
        $this->menu = $menu;
    }

    protected function beforeRender()
    {
        $this->template->menu = $this->menu;
    }
}
