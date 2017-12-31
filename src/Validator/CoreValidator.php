<?php

namespace Peldax\NetteInit\Validator;

final class CoreValidator
{
    const LENGTH = 'Peldax\NetteInit\Validator\CoreValidator::length';
    const SAME_LENGTH = 'Peldax\NetteInit\Validator\CoreValidator::sameLength';
    
    public static function length(\Nette\Forms\IControl $control, string $controlName)
    {
        return strlen($control->getValue()) == $control->getForm()[$controlName]->getValue();
    }
    
    public static function sameLength(\Nette\Forms\IControl $control, string $controlName)
    {
        return strlen($control->getValue()) == strlen($control->getForm()[$controlName]->getValue());
    }
}
