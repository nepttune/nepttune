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

namespace Nepttune\Presenter;

interface ILinkPresenter
{
    const INVALID_LINK_SILENT = 0b0000,
        INVALID_LINK_WARNING = 0b0001,
        INVALID_LINK_EXCEPTION = 0b0010,
        INVALID_LINK_TEXTUAL = 0b0100;
}
