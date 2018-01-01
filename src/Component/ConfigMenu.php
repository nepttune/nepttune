<?php

namespace Peldax\NetteInit\Component;

final class ConfigMenu extends BaseComponent
{
    /** @var array */
    protected $menu;

    public function __construct(array $menu)
    {
        $this->menu = $menu;
    }

    protected function beforeRender() : void
    {
        $this->template->menu = $this->menu;
    }
}
