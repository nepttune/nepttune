<?php

namespace Nepttune\Component;

final class Style extends BaseAssetComponent
{
    public function renderComponent()
    {
        $this->beforeRender();
        $this->template->setFile(str_replace(".php", "_component.latte", static::getReflection()->getFileName()));
        $this->template->render();
    }
}
