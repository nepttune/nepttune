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

if (isset($_SERVER['HTTP_X_FORWARDED_PROTO'], $_SERVER['SERVER_PORT']))
{
    if ($_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' && in_array($_SERVER['SERVER_PORT'], [80, 82], true))
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

$container = require __DIR__ . '/../app/bootstrap.php';
$container->getService('application')->run();
