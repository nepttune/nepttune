<?php

namespace Peldax\NetteInit;

final class Deploy
{
    const PROJECT_DIR = __DIR__ . '/../../../../';
    const FILES_DIR = __DIR__ . '/../copy/';

    const DIRS = [
        'app/model'
    ];

    public static function init()
    {
        echo 'Peldax\Init handler started.';

        self::recurseCopy(self::FILES_DIR, self::PROJECT_DIR);

        self::createDirs();

        echo 'Peldax\Init handler completed.';
    }

    private static function recurseCopy($src, $dst)
    {
        $dir = opendir($src);

        if (!is_dir($dst))
        {
            mkdir($dst);
        }

        while(false !== ( $file = readdir($dir)) )
        {
            if (( $file !== '.' ) && ( $file !== '..' ))
            {
                if ( is_dir($src . '/' . $file) )
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
            $dst = self::PROJECT_DIR . $dir;

            if (!is_dir($dst))
            {
                mkdir($dst);
            }
        }
    }
}
