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

abstract class BasePresenter extends \Nette\Application\UI\Presenter implements \Nepttune\TI\ITranslator
{
    use \IPub\MobileDetect\TMobileDetect;
    use \Nepttune\TI\TTranslator;

    /** @persistent */
    public $locale;

    /** @var  string */
    public $module;

    /** @var  string */
    public $nameWM;

    /** @var  \Nepttune\Component\IAssetLoaderFactory */
    protected $iAssetLoaderFactory;

    /** @var  array */
    protected $meta;

    /** @var  array */
    protected $dest;

    public function injectParameters(array $meta, array $dest, \Nepttune\Component\IAssetLoaderFactory $IAssetLoaderFactory)
    {
        $this->meta = $meta;
        $this->dest = $dest;
        $this->iAssetLoaderFactory = $IAssetLoaderFactory;
    }
    
    protected function startup()
    {
        $pos = strpos($this->getName(), ':');

        $this->module = $pos === false ? '' :               substr($this->getName(), 0, $pos);
        $this->nameWM = $pos === false ? $this->getName() : substr($this->getName(), $pos + 1);

        parent::startup();
    }

    protected function beforeRender()
    {
        $this->template->meta = $this->meta;
        $this->template->dest = $this->dest;

        parent::beforeRender();
    }

    public function flashMessage($message, $type = 'info') : \stdClass
    {
        $flash = parent::flashMessage($message, $type);

        if ($this->isAjax())
        {
            $this->redrawControl('flashMessages');
        }

        return $flash;
    }

    public function createComponent($name, array $args = null)
    {
        if (method_exists($this, 'createComponent'.ucfirst($name)))
        {
            return parent::createComponent($name);
        }

        if ($args !== null)
        {
            return $this->context->createService($name, $args);
        }

        return $this->context->createService($name);
    }

    public function actionCloseFancy($control = null, $rowId = null) : void
    {
        $this->getFlashSession()->setExpiration(time() + 5);

        $this->template->setFile(__DIR__.'/../templates/closeFancy.latte');

        $this->template->redrawControl = false;
        $this->template->redrawRow = false;

        if ($control && $rowId)
        {
            $this->template->redrawRow = true;
            $this->template->control = $control;
            $this->template->rowId = $rowId;
        }
        elseif ($control)
        {
            $this->template->redrawControl = true;
            $this->template->control = $control;
        }
    }

    public function getModule() : string
    {
        return $this->module;
    }

    public function getNameWM() : string
    {
        return $this->nameWM;
    }

    public function getId() : int
    {
        return (int) $this->getParameter('id');
    }
    
    public function findLayoutTemplateFile() : string
    {
        if ($this->layout)
        {
            return $this->layout;
        }
        
        $dir = \dirname(static::getReflection()->getFileName());
        $primary = $dir . '/../templates/@layout.latte';

        if (is_file($primary))
        {
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

    public static function getFlashArea() : string
    {
        return __DIR__ . '/../templates/flasharea.latte';
    }

    protected function createComponentAssetLoader()
    {
        return $this->iAssetLoaderFactory->create();
    }
}
