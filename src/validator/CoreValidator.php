<?php

namespace Peldax\NetteInit\Validator;

final class CoreValidator
{
    const GREATER_EQUAL = 'Peldax\NetteInit\Validator\CoreValidator::greaterEqual';
    const GREATER = 'Peldax\NetteInit\Validator\CoreValidator::greater';
    const LESS_EQUAL = 'Peldax\NetteInit\Validator\CoreValidator::lessEqual';
    const LESS = 'Peldax\NetteInit\Validator\CoreValidator::less';
    const LENGTH = 'Peldax\NetteInit\Validator\CoreValidator::length';
    const SAME_LENGTH = 'Peldax\NetteInit\Validator\CoreValidator::sameLength';

    public static function greaterEqual(\Nette\Forms\IControl $control, string $controlName)
    {
        return $control->getValue() >= $control->getForm()[$controlName]->getValue();
    }

    public static function greater(\Nette\Forms\IControl $control, string $controlName)
    {
        return $control->getValue() > $control->getForm()[$controlName]->getValue();
    }

    public static function lessEqual(\Nette\Forms\IControl $control, string $controlName)
    {
        return $control->getValue() <= $control->getForm()[$controlName]->getValue();
    }

    public static function less(\Nette\Forms\IControl $control, string $controlName)
    {
        return $control->getValue() < $control->getForm()[$controlName]->getValue();
    }
    
    public static function length(\Nette\Forms\IControl $control, string $controlName)
    {
        return strlen($control->getValue()) == $control->getForm()[$controlName]->getValue();
    }
    
    public static function sameLength(\Nette\Forms\IControl $control, string $controlName)
    {
        return strlen($control->getValue()) == strlen($control->getForm()[$controlName]->getValue());
    }
}
