<?php

namespace Peldax\NetteInit\Component;

class ConfigMenu extends BaseComponent
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
