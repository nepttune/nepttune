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

/**
 * This file uses modified code snippets from Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

declare(strict_types = 1);

namespace Nepttune\Presenter;

use Nette\Application\Helpers;

trait TTemplate
{
    /** @var \Kdyby\Redis\RedisStorage */
    private $cacheStorage;

    /** @var \Nette\Application\UI\ITemplate */
    private $template;

    private $layout;

    public function injectTemplate(\Kdyby\Redis\RedisStorage $storage)
    {
        $this->cacheStorage = $storage;
    }

    public function sendTemplate() : void
    {
        $template = $this->getTemplate();
        if (!$template->getFile()) {
            $files = $this->formatTemplateFiles();
            foreach ($files as $file) {
                if (is_file($file)) {
                    $template->setFile($file);
                    break;
                }
            }

            if (!$template->getFile()) {
                $file = preg_replace('#^.*([/\\\\].{1,70})\z#U', "\xE2\x80\xA6\$1", reset($files));
                $file = strtr($file, '/', DIRECTORY_SEPARATOR);
                $this->error("Page not found. Missing template '$file'.");
            }
        }

        $this->sendResponse(new \Nette\Application\Responses\TextResponse($template));
    }

    public function findLayoutTemplateFile()
    {
        if ($this->layout === false) {
            return;
        }
        $files = $this->formatLayoutTemplateFiles();
        foreach ($files as $file) {
            if (is_file($file)) {
                return $file;
            }
        }

        if ($this->layout) {
            $file = preg_replace('#^.*([/\\\\].{1,70})\z#U', "\xE2\x80\xA6\$1", reset($files));
            $file = strtr($file, '/', DIRECTORY_SEPARATOR);
            throw new \Nette\FileNotFoundException("Layout not found. Missing template '$file'.");
        }
    }

    public function formatLayoutTemplateFiles() : array
    {
        if (preg_match('#/|\\\\#', $this->layout)) {
            return [$this->layout];
        }
        list($module, $presenter) = Helpers::splitName($this->getName());
        $layout = $this->layout ?: 'layout';
        $dir = \dirname((new \Nette\Application\UI\ComponentReflection($this))->getFileName());
        $dir = is_dir("$dir/templates") ? $dir : \dirname($dir);
        $list = [
            "$dir/templates/$presenter/@$layout.latte",
            "$dir/templates/$presenter.@$layout.latte",
        ];
        do {
            $list[] = "$dir/templates/@$layout.latte";
            $dir = \dirname($dir);
        } while ($dir && $module && (list($module) = Helpers::splitName($module)));
        return $list;
    }

    public function formatTemplateFiles() : array
    {
        list(, $presenter) = Helpers::splitName($this->getName());
        $dir = \dirname((new \Nette\Application\UI\ComponentReflection($this))->getFileName());
        $dir = is_dir("$dir/templates") ? $dir : \dirname($dir);
        return [
            "$dir/templates/$presenter/$this->action.latte",
            "$dir/templates/$presenter.$this->action.latte",
        ];
    }

    public function getTemplate() : \Nette\Bridges\ApplicationLatte\Template
    {
        if (!$this->template) {
            $this->template = $this->createTemplate();
        }

        return $this->template;
    }

    public function createTemplate() : \Nette\Bridges\ApplicationLatte\Template
    {
        $latte = new \Latte\Engine();
        $template = new \Nette\Bridges\ApplicationLatte\Template($latte);
        $presenter = $control = $this;

        if ($latte->onCompile instanceof \Traversable) {
            $latte->onCompile = iterator_to_array($latte->onCompile);
        }

        array_unshift($latte->onCompile, function ($latte) {
            $latte->getCompiler()->addMacro('cache', new \Nette\Bridges\CacheLatte\CacheMacro($latte->getCompiler()));
            \Nette\Bridges\ApplicationLatte\UIMacros::install($latte->getCompiler());
        });

        foreach (['normalize', 'toAscii', 'webalize', 'reverse'] as $name) {
            $latte->addFilter($name, 'Nette\Utils\Strings::' . $name);
        }
        $latte->addFilter('null', function () {});
        $latte->addFilter('modifyDate', function ($time, $delta, $unit = null) {
            return $time == null ? null : \Nette\Utils\DateTime::from($time)->modify($delta . $unit); // intentionally ==
        });

        if (!isset($latte->getFilters()['translate'])) {
            $latte->addFilter('translate', function (\Latte\Runtime\FilterInfo $fi) {
                throw new \Nette\InvalidStateException('Translator has not been set. Set translator using $template->setTranslator().');
            });
        }

        $template->baseUri = $template->baseUrl = $this->getHttpRequest() ? rtrim($this->getHttpRequest()->getUrl()->getBaseUrl(), '/') : null;
        $template->basePath = preg_replace('#https?://[^/]+#A', '', $template->baseUrl);
        $template->control = $control;
        $template->presenter = $presenter;

        $nonce = $presenter && preg_match('#\s\'nonce-([\w+/]+=*)\'#', $presenter->getHttpResponse()->getHeader('Content-Security-Policy'), $m) ? $m[1] : null;
        $latte->addProvider('uiNonce', $nonce);
        $latte->addProvider('cacheStorage', $this->cacheStorage);

        if (\in_array('Nepttune\Presenter\TLink', \class_uses($this), true))
        {
            $latte->addProvider('uiControl', $control);
            $latte->addProvider('uiPresenter', $presenter);
        }

        return $template;
    }
}
