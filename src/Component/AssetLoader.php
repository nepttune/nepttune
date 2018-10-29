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

final class AssetLoader extends BaseComponent implements IStyleLists, IScriptLists
{
    /** @var bool */
    protected $admin;

    /** @var  string */
    protected $module;

    /** @var  string */
    protected $presen;

    /** @var  string */
    protected $action;

    /** @var string */
    protected $vapidPublicKey;

    /** @var \Nette\Caching\Cache */
    protected $cache;

    public function __construct(string $vapidPublicKey, \Nette\Caching\IStorage $storage)
    {
        parent::__construct();

        $this->vapidPublicKey = $vapidPublicKey;
        $this->cache = new \Nette\Caching\Cache($storage, 'Nepttune.AssetLoader');
    }

    protected function attached($presenter) : void
    {
        $this->admin =
            \class_exists('\Nepttune\Presenter\BaseAuthPresenter') &&
            $presenter instanceof \Nepttune\Presenter\BaseAuthPresenter;
        $this->module = $presenter->getModule();
        $this->presen = $presenter->getName();
        $this->action = $presenter->getAction();

        if (!$this->module)
        {
            throw new \Nette\InvalidStateException();
        }
    }

    public function renderHead() : void
    {
        $assets = $this->getAssetsHead();

        $this->template->variables = false;
        $this->template->styles = $assets[0];
        $this->template->scripts = $assets[1];
        $this->template->recaptcha = false;

        parent::render();
    }

    public function renderBody() : void
    {
        $assets = $this->getAssetsBody();

        $this->template->variables = true;
        $this->template->vapidPublicKey = $this->vapidPublicKey;
        $this->template->styles = $assets[0];
        $this->template->scripts = $assets[1];
        $this->template->recaptcha = $assets[2];

        parent::render();
    }

    public function getIntegrity(string $path) : string
    {
        return $this->cache->call('Nepttune\Component\AssetLoader::generateChecksum', $path);
    }

    public static function generateChecksum(string $path) : string
    {
        return 'sha256-' . base64_encode(hash_file('sha256', __DIR__ . '/../../../../../www' . $path, true));
    }

    public function getAssetsHead() : array
    {
        $cacheName = "{$this->module}_{$this->presen}_{$this->action}_head";
        $assets = $this->cache->load($cacheName);

        if ($assets)
        {
            return $assets;
        }
        
        $styles = static::STYLE_HEAD;
        $scripts = static::SCRIPT_HEAD;

        if ($this->admin)
        {
            $styles = array_merge($styles, static::STYLE_HEAD_ADMIN);
            $scripts = array_merge($scripts, static::SCRIPT_HEAD_ADMIN);
        }
        else
        {
            $styles = array_merge($styles, static::STYLE_HEAD_FRONT);
            $scripts = array_merge($scripts, static::SCRIPT_HEAD_FRONT);
        }

        if ($this->module)
        {
            $moduleStyle = '/scss/module/' . $this->module . '.css';
            if (file_exists(getcwd() . '/node_modules/nepttune' . $moduleStyle)) {
                $styles[] = '/node_modules/nepttune' . $moduleStyle;
            }
            if (file_exists(getcwd() . $moduleStyle)) {
                $styles[] = $moduleStyle;
            }
        }

        $presenStyle = '/scss/presenter/' . $this->presen . '.css';
        if (file_exists(getcwd() . '/node_modules/nepttune' . $presenStyle))
        {
            $styles[] = '/node_modules/nepttune' . $presenStyle;
        }
        if (file_exists(getcwd() . $presenStyle))
        {
            $styles[] = $presenStyle;
        }

        $actionStyle = '/scss/action/' . $this->presen . '/' . $this->action . '.css';
        if (file_exists(getcwd() . '/node_modules/nepttune' . $actionStyle))
        {
            $styles[] = '/node_modules/nepttune' . $actionStyle;
        }
        if (file_exists(getcwd() . $actionStyle))
        {
            $styles[] = $actionStyle;
        }

        $assets = [$styles, $scripts];
        $this->cache->save($cacheName, $assets);
        return $assets;
    }

    public function getAssetsBody() : array
    {
        $cacheName = "{$this->module}_{$this->presen}_{$this->action}_body";
        $assets = $this->cache->load($cacheName);

        if ($assets)
        {
            return $assets;
        }
        
        $styles = static::STYLE_BODY;
        $scripts = static::SCRIPT_BODY;

        if ($this->admin)
        {
            $styles = array_merge($styles, static::STYLE_BODY_ADMIN);
            $scripts = array_merge($scripts, static::SCRIPT_BODY_ADMIN);
        }
        else
        {
            $styles = array_merge($styles, static::STYLE_BODY_FRONT);
            $scripts = array_merge($scripts, static::SCRIPT_BODY_FRONT);
        }

        $hasForm = false;
        $hasList = false;
        $hasStat = false;

        foreach ($this->getPresenter()->getComponents() as $name => $component)
        {
            $componentStyle = '/scss/component/' . ucfirst($name) . '.css';
            $componentScript = '/js/component/' . ucfirst($name) . '.js';

            if (file_exists(getcwd() . '/node_modules/nepttune' . $componentStyle))
            {
                $styles[] = '/node_modules/nepttune' . $componentStyle;
            }
            if (file_exists(getcwd() . $componentStyle))
            {
                $styles[] = $componentStyle;
            }

            if (file_exists(getcwd() . '/node_modules/nepttune' . $componentScript))
            {
                $scripts[] = '/node_modules/nepttune' . $componentScript;
            }
            if (file_exists(getcwd() . $componentScript))
            {
                $scripts[] = $componentScript;
            }

            if (!$hasForm && strpos($name, 'Form') !== false)
            {
                $hasForm = true;
            }

            if (!$hasList && strpos($name, 'List') !== false)
            {
                $hasForm = true;
                $hasList = true;
            }

            if (!$hasStat && strpos($name, 'Stat') !== false)
            {
                $hasStat = true;
            }
        }

        if ($hasForm)
        {
            $styles = array_merge($styles, static::STYLE_FORM);
            $scripts = array_merge($scripts, static::SCRIPT_FORM);
        }

        if ($hasList)
        {
            $styles = array_merge($styles, static::STYLE_LIST);
            $scripts = array_merge($scripts, static::SCRIPT_LIST);
        }

        if ($hasStat)
        {
            $styles = array_merge($styles, static::STYLE_STAT);
            $scripts = array_merge($scripts, static::SCRIPT_STAT);
        }

        if ($this->module)
        {
            $moduleScript = '/js/module/' . $this->module . '.js';
            if (file_exists(getcwd() . '/node_modules/nepttune' . $moduleScript)) {
                $scripts[] = '/node_modules/nepttune' . $moduleScript;
            }
            if (file_exists(getcwd() . $moduleScript)) {
                $scripts[] = $moduleScript;
            }
        }

        $presenScript = '/js/presenter/' . $this->presen . '.js';
        if (file_exists(getcwd() . '/node_modules/nepttune' . $presenScript))
        {
            $scripts[] = '/node_modules/nepttune' . $presenScript;
        }
        if (file_exists(getcwd() . $presenScript))
        {
            $scripts[] = $presenScript;
        }

        $actionScript = '/js/action/' . $this->presen . '/' . $this->action . '.js';
        if (file_exists(getcwd() . '/node_modules/nepttune' . $actionScript))
        {
            $scripts[] = '/node_modules/nepttune' . $actionScript;
        }
        if (file_exists(getcwd() . $actionScript))
        {
            $scripts[] = $actionScript;
        }

        $assets = [$styles, $scripts, $hasForm];
        $this->cache->save($cacheName, $assets);
        return $assets;
    }
}
