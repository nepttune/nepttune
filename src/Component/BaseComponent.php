<?php

namespace Peldax\NetteInit\Component;

abstract class BaseComponent extends \Nette\Application\UI\Control
{
    protected function getPost()
    {
        return $this->getPresenter()->getPost();
    }

    protected function createComponent($name, array $args = null)
    {
        if (method_exists($this, 'createComponent'.ucfirst($name)))
        {
            return parent::createComponent($name);
        }

        return $this->getPresenter()->createComponent($name, $args);
    }

    protected function beforeRender() : void
    {
    }

    public function render() : void
    {
        $this->beforeRender();
        $this->template->setFile(str_replace(".php", ".latte", static::getReflection()->getFileName()));
        $this->template->render();
    }
}
