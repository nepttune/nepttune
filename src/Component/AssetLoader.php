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

    /** @var  string */
    protected $adminModule;

    /** @var \Nette\Caching\Cache */
    private $cache;

    public function __construct(string $adminModule, \Kdyby\Redis\RedisStorage $redisStorage)
    {
        $this->adminModule = ucfirst($adminModule);
        $this->cache = new \Nette\Caching\Cache($redisStorage);
    }

    public function attached($presenter) : void
    {
        $this->admin = \class_exists('\Nepttune\Presenter\BaseAuthPresenter') && $presenter instanceof \Nepttune\Presenter\BaseAuthPresenter;
        $this->module = $presenter->getModule() ?: $this->adminModule;
        $this->presen = $presenter->getName();
        $this->action = $presenter->getAction();
    }

    public function renderHead() : void
    {
        $assets = $this->cache->call([$this, 'getAssetsHead']);

        $this->template->variables = false;
        $this->template->styles = $assets[0];
        $this->template->scripts = $assets[1];
        $this->template->recaptcha = false;

        parent::render();
    }

    public function renderBody() : void
    {
        $assets = $this->cache->call([$this, 'getAssetsBody']);

        $this->template->variables = true;
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
        return 'sha256-' . base64_encode(hash_file('sha256', getcwd() . $path, true));
    }

    public function getAssetsHead() : array
    {
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
            $moduleStyle = '/scss/module/' . $this->module . '.min.css';
            if (file_exists(getcwd() . '/node_modules/nepttune' . $moduleStyle)) {
                $styles[] = '/node_modules/nepttune' . $moduleStyle;
            }
            if (file_exists(getcwd() . $moduleStyle)) {
                $styles[] = $moduleStyle;
            }
        }

        $presenStyle = '/scss/presenter/' . $this->presen . '.min.css';
        if (file_exists(getcwd() . '/node_modules/nepttune' . $presenStyle))
        {
            $styles[] = '/node_modules/nepttune' . $presenStyle;
        }
        if (file_exists(getcwd() . $presenStyle))
        {
            $styles[] = $presenStyle;
        }

        $actionStyle = '/scss/action/' . $this->presen . '/' . $this->action . '.min.css';
        if (file_exists(getcwd() . '/node_modules/nepttune' . $actionStyle))
        {
            $styles[] = '/node_modules/nepttune' . $actionStyle;
        }
        if (file_exists(getcwd() . $actionStyle))
        {
            $styles[] = $actionStyle;
        }

        return [$styles, $scripts];
    }

    public function getAssetsBody() : array
    {
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
            $componentStyle = '/scss/component/' . $name . '.min.css';
            $componentScript = '/js/component/' . $name . '.min.js';

            if (file_exists(getcwd() . $componentStyle))
            {
                $styles[] = $componentStyle;
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
            $moduleScript = '/js/module/' . $this->module . '.min.js';
            if (file_exists(getcwd() . '/node_modules/nepttune' . $moduleScript)) {
                $scripts[] = '/node_modules/nepttune' . $moduleScript;
            }
            if (file_exists(getcwd() . $moduleScript)) {
                $scripts[] = $moduleScript;
            }
        }

        $presenScript = '/js/presenter/' . $this->presen . '.min.js';
        if (file_exists(getcwd() . '/node_modules/nepttune' . $presenScript))
        {
            $scripts[] = '/node_modules/nepttune' . $presenScript;
        }
        if (file_exists(getcwd() . $presenScript))
        {
            $scripts[] = $presenScript;
        }

        $actionScript = '/js/action/' . $this->presen . '/' . $this->action . '.min.js';
        if (file_exists(getcwd() . '/node_modules/nepttune' . $actionScript))
        {
            $scripts[] = '/node_modules/nepttune' . $actionScript;
        }
        if (file_exists(getcwd() . $actionScript))
        {
            $scripts[] = $actionScript;
        }

        return [$styles, $scripts, $hasForm];
    }
}
