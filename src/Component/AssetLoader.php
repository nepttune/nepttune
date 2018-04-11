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
    /** @var \Kdyby\Redis\RedisStorage */
    public $storage;

    public function __construct(\Kdyby\Redis\RedisStorage $redisStorage)
    {
        $this->storage = $redisStorage;
    }

    /*** @var bool */
    protected $admin;

    /** @var  array */
    protected $viewStyles = [];

    /** @var array  */
    protected $viewScripts = [];

    public function attached($presenter)
    {
        $this->admin = $presenter instanceof \Nepttune\Presenter\BaseAuthPresenter;

        $module = $presenter->getModule();
        $presen = $presenter->getName();
        $action = $presenter->getAction();

        $moduleStyle = '/scss/module/' . $module . '.min.css';
        if (file_exists(getcwd() . '/node_modules/nepttune' . $moduleStyle))
        {
            $this->viewStyles[] = '/node_modules/nepttune' . $moduleStyle;
        }
        if (file_exists(getcwd() . $moduleStyle))
        {
            $this->viewStyles[] = $moduleStyle;
        }

        $moduleScript = '/js/module/' . $module . '.min.js';
        if (file_exists(getcwd() . '/node_modules/nepttune' . $moduleScript))
        {
            $this->viewScripts[] = '/node_modules/nepttune' . $moduleScript;
        }
        if (file_exists(getcwd() . $moduleScript))
        {
            $this->viewScripts[] = $moduleScript;
        }

        $presenStyle = '/scss/presenter/' . $presen . '.min.css';
        if (file_exists(getcwd() . '/node_modules/nepttune' . $presenStyle))
        {
            $this->viewStyles[] = '/node_modules/nepttune' . $presenStyle;
        }
        if (file_exists(getcwd() . $presenStyle))
        {
            $this->viewStyles[] = $presenStyle;
        }

        $presenScript = '/js/presenter/' . $presen . '.min.js';
        if (file_exists(getcwd() . '/node_modules/nepttune' . $presenScript))
        {
            $this->viewScripts[] = '/node_modules/nepttune' . $presenScript;
        }
        if (file_exists(getcwd() . $presenScript))
        {
            $this->viewScripts[] = $presenScript;
        }

        $actionStyle = '/scss/action/' . $presen . '/' . $action . '.min.css';
        if (file_exists(getcwd() . '/node_modules/nepttune' . $actionStyle))
        {
            $this->viewStyles[] = '/node_modules/nepttune' . $actionStyle;
        }
        if (file_exists(getcwd() . $actionStyle))
        {
            $this->viewStyles[] = $actionStyle;
        }

        $actionScript = '/js/action/' . $presen . '/' . $action . '.min.js';
        if (file_exists(getcwd() . '/node_modules/nepttune' . $actionScript))
        {
            $this->viewScripts[] = '/node_modules/nepttune' . $actionScript;
        }
        if (file_exists(getcwd() . $actionScript))
        {
            $this->viewScripts[] = $actionScript;
        }
    }

    public function renderHead()
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

        $styles = array_merge($styles, $this->viewStyles);

        $this->template->variables = false;
        $this->template->recaptcha = false;
        $this->template->styles = $styles;
        $this->template->scripts = $scripts;

        parent::render();
    }

    public function renderBody()
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

        $scripts = array_merge($scripts, $this->viewScripts);

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

        $this->template->variables = true;
        $this->template->recaptcha = $hasForm;
        $this->template->styles = $styles;
        $this->template->scripts = $scripts;

        parent::render();
    }

    public function getIntegrity(string $path) : string
    {
        $cache = new \Nette\Caching\Cache($this->storage);

        return $cache->call('Nepttune\Component\AssetLoader::generateChecksum', $path);
    }

    public static function generateChecksum(string $path) : string
    {
        return 'sha256-' . base64_encode(hash_file('sha256', getcwd() . $path, true));
    }
}
