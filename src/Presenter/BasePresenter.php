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

namespace Nepttune\Presenter;

/**
 * Class BasePresenter
 * @package Nepttune\Presenter
 * @property \Nette\Bridges\ApplicationLatte\Template $template
 */
abstract class BasePresenter extends \Nette\Application\UI\Presenter
    implements \Nepttune\TI\IAssetPresenter, \Nepttune\TI\ITranslator
{
    use \IPub\FlashMessages\TFlashMessages;
    use \IPub\MobileDetect\TMobileDetect;
    use \Nepttune\TI\TAssetPresenter;
    use \Nepttune\TI\TTranslator;

    /** @persistent */
    public $locale;

    /** @var array */
    protected $meta;

    /** @var array */
    protected $dest;

    public function startup()
    {
        parent::startup();

        if ($this->isAjax()) {
            $this->getComponent('flashMessages');
        }
    }

    public function decorateParameters(array $meta, array $dest)
    {
        $this->meta = $meta;
        $this->dest = $dest;
    }

    protected function beforeRender() : void
    {
        $this->template->meta = $this->meta;
        $this->template->dest = $this->dest;

        parent::beforeRender();
    }
    
    public function createComponent(string $name) : ?\Nette\ComponentModel\IComponent
    {
        if (\method_exists($this, 'createComponent' . \ucfirst($name))) {
            return parent::createComponent($name);
        }

        return $this->context->createService($name);
    }

    public function getId() : int
    {
        return (int) $this->getParameter('id');
    }
    
    public function findLayoutTemplateFile() : string
    {
        if ($this->layout) {
            return $this->layout;
        }

        $primary = \dirname(static::getReflection()->getFileName()) . '/../templates/@layout.latte';

        if (\is_file($primary)) {
            return $primary;
        }

        return static::getDefaultLayout();
    }

    public static function getDefaultLayout() : string
    {
        return static::getCoreLayout();
    }

    public static function getAjaxLayout() : string
    {
        return __DIR__ . '/../templates/@ajax.latte';
    }

    public static function getIframeLayout() : string
    {
        return __DIR__ . '/../templates/@iframe.latte';
    }

    public static function getCoreLayout() : string
    {
        return __DIR__ .'/../templates/@core.latte';
    }
    
    public static function getCookiePopup() : string
    {
        return __DIR__ . '/../templates/cookiePopup.latte';
    }
    
    public static function getLocaleSelect() : string
    {
        return __DIR__ . '/../templates/localeSelect.latte';
    }
    
    public static function getPaginator() : string
    {
        return __DIR__ . '/../templates/paginator.latte';
    }
}
