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

foreach (['nepttune', 'admin'] as $extension) {
    $coreFile = __DIR__ . '/../vendor/nepttune/' . $extension . '/config/core.neon';
    $debugFile = __DIR__ . '/../vendor/nepttune/' . $extension . '/config/debug.neon';

    if (\file_exists($coreFile)) {
        $configurator->addConfig($coreFile);
    }

    if (!$debugMode || !\file_exists($debugFile)) {
        continue;
    }

    $configurator->addConfig($debugFile);
}

$configurator->addConfig(__DIR__ . '/config/core.neon');

if (\PHP_SAPI === 'cli') {
    $configurator->addConfig(__DIR__ . '/../vendor/nepttune/nepttune/config/cli.neon');
    $configurator->addConfig(__DIR__ . '/config/cli.neon');
    $configurator->setTempDirectory(__DIR__ . '/../temp/console');
}

return $configurator->createContainer();
