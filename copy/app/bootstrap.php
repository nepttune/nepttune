<?php

require __DIR__ . '/../vendor/autoload.php';

$configurator = new Nette\Configurator;
$configurator->setDebugMode(true);
$configurator->enableDebugger(__DIR__ . '/../log');
$configurator->setTempDirectory(__DIR__ . '/../temp');
$configurator->createRobotLoader()
    ->addDirectory(__DIR__)
    ->addDirectory(__DIR__ . '/../vendor/nepttune/')
    ->register();

$configurator->addConfig(__DIR__ . '/../vendor/nepttune/nepttune/config/core.neon');
foreach (['admin'] as $extension)
{
    $file = __DIR__ . "/../vendor/nepttune/{$extension}/config/core.neon";
    if (file_exists($file))
    {
        $configurator->addConfig($file);
    }
}
$configurator->addConfig(__DIR__ . '/config/core.neon');

return $configurator->createContainer();
