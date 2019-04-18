<?php

/**
 * This file is part of Nepttune (https://www.peldax.com)
 *
 * Copyright (c) 2018 Václav Pelíšek (info@peldax.com)
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license. For more information, see
 * <https://www.peldax.com>.
 */

declare(strict_types = 1);

namespace Nepttune\Component;

/**
 * Class BaseComponent
 * @package Nepttune\Component
 * @property \Nette\Bridges\ApplicationLatte\Template $template
 */
abstract class BaseComponent extends \Nette\Application\UI\Control
{
    protected const TEMPLATE_PATH = false;
    
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
        $this->template->setFile(static::TEMPLATE_PATH ?: str_replace('.php', '.latte', static::getReflection()->getFileName()));
        $this->template->render();
    }
}
