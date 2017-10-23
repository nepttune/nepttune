<?php

namespace App\Component;

class Menu extends BaseComponent
{
    /** @var array */
    protected $menu;

    public function __construct(array $menu)
    {
        $this->menu = $menu;
    }

    public function beforeRender()
    {
        $this->template->menu = $this->menu;
    }
}
