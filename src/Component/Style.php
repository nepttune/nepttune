<?php

namespace Peldax\NetteInit\Component;

final class Style extends BaseComponent
{
    public function renderComponent()
    {
        $this->beforeRender();
        $this->template->setFile(str_replace(".php", "_component.latte", static::getReflection()->getFileName()));
        $this->template->render();
    }
}
