<?php

namespace Peldax\NetteInit\Component;

final class ConfigNavbar extends BaseComponent
{
    /** @var array */
    protected $navbar;

    public function __construct(array $navbar)
    {
        $this->navbar = $navbar;
    }

    protected function beforeRender() : void
    {
        $this->template->background = $this->navbar['background'];
        $this->template->brand = $this->navbar['brand'];
        $this->template->items = $this->navbar['items'];
    }
}
