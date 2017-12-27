<?php

namespace Peldax\NetteInit;

final class Deploy
{
    const TARGET_DIR = __DIR__ . '/../../../../';
    const SOURCE_DIR = __DIR__ . '/../copy/';

    const DOCKER_TARGET_DIR = self::TARGET_DIR . 'docker/';
    const DOCKER_SOURCE_DIR = self::SOURCE_DIR . 'docker/';
    const DOCKER_FILES = [
        'bin/startup.sh',
        'docker-compose.yml',
        'dockerfile-apache-php'
    ];

    const DIRS = [
        'app/model',
        'app/component',
        'www/js/module',
        'www/js/presenter',
        'www/js/action',
        'www/scss/module',
        'www/scss/presenter',
        'www/scss/action'
    ];

    public static function init()
    {
        echo 'Peldax\Init handler started.' . PHP_EOL;

        self::recurseCopy(self::SOURCE_DIR, self::TARGET_DIR);

        self::createDirs();

        echo 'Peldax\Init handler completed.' . PHP_EOL;
    }

    private static function recurseCopy($src, $dst)
    {
        $dir = opendir($src);

        if ($src === self::DOCKER_SOURCE_DIR)
        {
            $change = false;

            foreach (self::DOCKER_FILES as $file)
            {
                if (!file_exists(self::DOCKER_TARGET_DIR . $file) ||
                    hash_file('sha256̈́', self::DOCKER_SOURCE_DIR . $file) === hash_file('sha256', self::DOCKER_TARGET_DIR . $file))
                {
                    $change = true;
                    break;
                }
            }

            if ($change)
            {
                echo "\033[31m WARNING: Docker files has changed, reload docker container. \033[0m" . PHP_EOL;
            }
        }

        if (!is_dir($dst))
        {
            mkdir($dst);
        }

        while(false !== ($file = readdir($dir)))
        {
            if (($file !== '.') && ($file !== '..'))
            {
                if (is_dir($src . '/' . $file))
                {
                    self::recurseCopy($src . '/' . $file,$dst . '/' . $file);
                }
                else
                {
                    copy($src . '/' . $file,$dst . '/' . $file);
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
