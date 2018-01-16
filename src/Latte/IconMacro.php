<?php

namespace Peldax\NetteInit\Latte;

class IconMacro extends \Latte\Macros\MacroSet
{
    public static function install(\Latte\Compiler $compiler)
    {
        $set = new static($compiler);
        $set->addMacro('icon', function($node, $writer)
        {
            return $writer->write('echo \Peldax\NetteInit\Latte\IconMacro::renderIcon(%node.word, %node.array)');
        });
    }

    public static function renderIcon($icon, array $params = [])
    {
        $el = \Nette\Utils\Html::el('i');
        $el->addAttributes(['class' => 'fa fa-fw fa-'.$icon]);

        if (isset($params['size']))
        {
            $el->appendAttribute('class', 'fa-'.$params['size'].'x');
        }

        return $el;
    }
}
