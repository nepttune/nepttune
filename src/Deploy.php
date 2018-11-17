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

namespace Nepttune;

final class Deploy
{
    const TARGET_DIR = __DIR__ . '/../../../../';
    const SOURCE_DIR = __DIR__ . '/../copy/';

    const DIRS = [
        'app/command',
        'app/config',
        'app/consumer',
        'app/lang',
        'app/enum',
        'app/model',
        'app/module',
        'app/table',
        'app/TI',
        'www/js/action',
        'www/js/component',
        'www/js/module',
        'www/js/presenter',
        'www/scss/action',
        'www/scss/component',
        'www/scss/module',
        'www/scss/presenter'
    ];

    public static function init()
    {
        echo 'Nepttune handler started.' . PHP_EOL;

        self::recurseCopy(self::SOURCE_DIR, self::TARGET_DIR);
        self::createDirs();

        echo 'Nepttune handler completed.' . PHP_EOL;
    }

    private static function recurseCopy($src, $dst)
    {
        $dir = opendir($src);

        if (!is_dir($dst))
        {
            mkdir($dst);
        }

        while(false !== ($file = readdir($dir)))
        {
            if (($file !== '.') && ($file !== '..'))
            {
                if (is_dir($src . $file))
                {
                    self::recurseCopy($src . $file . '/', $dst . $file . '/');
                }
                else
                {
                    copy($src . $file, $dst . $file);
                }
            }
        }

        closedir($dir);
    }

    private static function createDirs()
    {
        foreach (self::DIRS as $dir)
        {
            $dst = self::TARGET_DIR . $dir;

            if (!is_dir($dst))
            {
                mkdir($dst);
            }
        }
    }
}
