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
        $this->template->background = isset($this->navbar['background']) ?: false;
        $this->template->breakpoint = isset($this->navbar['breakpoint']) ?: false;
        $this->template->brand = isset($this->navbar['brand']) ?: false;
        $this->template->items = $this->navbar['items'];
    }
}
