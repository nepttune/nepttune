<?php

/**
 * This file is part of Nepttune (https://www.peldax.com)
 *
 * Copyright (c) 2020 Václav Pelíšek (info@peldax.com)
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license. For more information, see
 * <https://www.peldax.com>.
 */

declare(strict_types = 1);

namespace Nepttune\TI;

trait TFlash
{
    /** @var \IPub\FlashMessages\FlashNotifier */
    public $flash;

    public function decorateFlash(\IPub\FlashMessages\FlashNotifier $flashNotifier)
    {
        $this->flash = $flashNotifier;
    }
}
