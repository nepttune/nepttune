<?php

$debugMode = true;

require __DIR__ . '/../vendor/autoload.php';

$configurator = new Nette\Configurator;
$configurator->setDebugMode($debugMode);
$configurator->enableDebugger(__DIR__ . '/../log');
$configurator->setTempDirectory(__DIR__ . '/../temp');
$configurator->createRobotLoader()
    ->addDirectory(__DIR__)
    ->addDirectory(__DIR__ . '/../vendor/nepttune/')
    ->register();

foreach (['nepttune', 'admin'] as $extension)
{
    $coreFile = __DIR__ . "/../vendor/nepttune/{$extension}/config/core.neon";
    $debugFile = __DIR__ . "/../vendor/nepttune/{$extension}/config/debug.neon";

    if (file_exists($coreFile))
    {
        $configurator->addConfig($coreFile);
    }

    if ($debugMode && file_exists($debugFile))
    {
        $configurator->addConfig($debugFile);
    }
}
$configurator->addConfig(__DIR__ . '/config/core.neon');

return $configurator->createContainer();
