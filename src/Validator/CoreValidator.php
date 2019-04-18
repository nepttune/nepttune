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

namespace Nepttune\Validator;

final class CoreValidator
{
    public static function sameLength(\Nette\Forms\IControl $control, string $controlName) : bool
    {
        return \mb_strlen($control->getValue()) === \mb_strlen($control->getForm()[$controlName]->getValue());
    }
    
    public static function isIn(\Nette\Forms\IControl $control, string $value) : bool
    {
        return \is_array($control->getValue()) && \in_array($value, $control->getValue(), true);
    }
}
