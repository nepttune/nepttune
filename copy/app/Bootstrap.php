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

namespace App;

final class Bootstrap
{
    public const DEBUG_MODE = true;

    public static function boot() : \Nette\Configurator
    {
        $configurator = new \Nette\Configurator();
        $configurator->setDebugMode(self::DEBUG_MODE);
        $configurator->enableTracy(__DIR__ . '/../log');
        $configurator->setTempDirectory(__DIR__ . '/../temp');
        $configurator->createRobotLoader()
            ->addDirectory(__DIR__ . '/../app/Module')
            ->addDirectory(__DIR__ . '/../vendor/nepttune/nepttune/src/Presenter')
            ->register();

        self::loadConfig($configurator, 'core');

        if (\PHP_SAPI === 'cli') {
            self::loadConfig($configurator, 'cli');
            $configurator->setTempDirectory(__DIR__ . '/../temp/console');
        }

        if (self::DEBUG_MODE) {
            self::loadConfig($configurator, 'debug');
        }

        return $configurator;
    }

    public static function loadConfig(\Nette\Configurator $configurator, string $file) : void
    {
        foreach (['nepttune', 'admin'] as $extension) {
            $nepttuneConfig = __DIR__ . '/../vendor/nepttune/' . $extension . '/config/' . $file . '.neon';

            if (!\file_exists($nepttuneConfig)) {
                continue;
            }

            $configurator->addConfig($nepttuneConfig);
        }

        $appConfig = __DIR__ . '/config/' . $file . '.neon';

        if (!\file_exists($appConfig)) {
            return;
        }

        $configurator->addConfig($appConfig);
    }

    public static function validateGlobals() : void
    {
        if (isset($_SERVER['HTTP_X_FORWARDED_PROTO'], $_SERVER['SERVER_PORT']))
        {
            if ($_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' && \in_array((int) $_SERVER['SERVER_PORT'], [80, 82], true))
            { // https over proxy
                $_SERVER['HTTPS'] = 'On';
                $_SERVER['SERVER_PORT'] = 443;
            }
            elseif ($_SERVER['HTTP_X_FORWARDED_PROTO'] === 'http' && (int) $_SERVER['SERVER_PORT'] === 80)
            { // http over proxy
                $_SERVER['HTTPS'] = 'Off';
                $_SERVER['SERVER_PORT'] = 80;
            }
        }
    }
}
