<?php

namespace App\Component;

abstract class BaseComponent extends \Nette\Application\UI\Control
{
    protected function getPost()
    {
        return $this->getPresenter()->getPost();
    }

    public function createComponent($name, array $args = null)
    {
        if (method_exists($this, 'createComponent'.ucfirst($name)))
        {
            return parent::createComponent($name);
        }

        return $this->getPresenter()->createComponent($name, $args);
    }

    public function beforeRender()
    {
    }

    public function render()
    {
        $this->beforeRender();
        $this->template->setFile(str_replace(".php", ".latte", static::getReflection()->getFileName()));
        $this->template->render();
    }
}
