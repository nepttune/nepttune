<?php

namespace Peldax;

final class Init
{
    public static function init()
    {
        $projectDir = __DIR__ . '/../../../';
        $filesDir = __DIR__ . '/src/';

        echo 'Peldax\Init handler started.';

        self::recurseCopy($filesDir, $projectDir);

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

}
