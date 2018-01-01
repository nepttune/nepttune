<?php

namespace Peldax\NetteInit\Validator;

final class CoreValidator
{
    const SAME_LENGTH = 'Peldax\NetteInit\Validator\CoreValidator::sameLength';
    
    public static function sameLength(\Nette\Forms\IControl $control, string $controlName)
    {
        return mb_strlen($control->getValue()) === mb_strlen($control->getForm()[$controlName]->getValue());
    }
}
