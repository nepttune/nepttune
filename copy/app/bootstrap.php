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

$debugMode = false;

require __DIR__ . '/../vendor/autoload.php';

$configurator = new Nette\Configurator();
$configurator->setDebugMode($debugMode);
$configurator->enableDebugger(__DIR__ . '/../log');
$configurator->setTempDirectory(__DIR__ . '/../temp');
$configurator->createRobotLoader()
    ->addDirectory(__DIR__)
    ->addDirectory(__DIR__ . '/../vendor/nepttune/')
    ->register();

$load = function (string $file) use ($configurator) {
    foreach (['nepttune', 'admin'] as $extension) {
        $libConfig = __DIR__ . '/../vendor/nepttune/' . $extension . '/config/' . $file . '.neon';

        if (\file_exists($libConfig)) {
            $configurator->addConfig($libConfig);
        }
    }

    $appConfig = __DIR__ . '/config/' . $file . '.neon';

    if (\file_exists($libConfig)) {
        $configurator->addConfig($appConfig);
    }
};

$load('core');

if (\PHP_SAPI === 'cli') {
    $load('cli');
    $configurator->setTempDirectory(__DIR__ . '/../temp/console');
}

if ($debugMode) {
    $load('debug');
}

return $configurator->createContainer();

