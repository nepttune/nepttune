<?php

namespace Peldax\NetteInit\Latte;

class IconMacro extends \Latte\Macros\MacroSet
{
    public static function install(\Latte\Compiler $compiler)
    {
        $set = new static($compiler);
        $set->addMacro('icon', function($node, $writer)
        {
            return $writer->write('echo \Peldax\NetteInit\Latte\IconMacro::renderIcon(%node.word)');
        });
    }

    public static function renderIcon($icon)
    {
        $el = \Nette\Utils\Html::el('i');
        $el->addAttributes(['class' => 'fa fa-'.$icon]);

        return $el;
    }
}
