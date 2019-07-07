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

require __DIR__ . '/../vendor/autoload.php';

\App\Bootstrap::validateGlobals();
\App\Bootstrap::boot()
    ->createContainer()
    ->getByType(Nette\Application\Application::class)
	->run();

