<?php

require __DIR__ . '/../vendor/autoload.php';

$configurator = new Nette\Configurator;
$configurator->setDebugMode(true);
$configurator->enableDebugger(__DIR__ . '/../log');
$configurator->setTempDirectory(__DIR__ . '/../temp');
$configurator->addConfig(__DIR__ . '/config/core.neon');
$configurator->createRobotLoader()
    ->addDirectory(__DIR__)
    ->register();

return $configurator->createContainer();
