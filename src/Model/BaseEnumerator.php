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

namespace Nepttune\Component;

use \Ublaboo\DataGrid\DataGrid;

class BaseEnumerator extends BaseListComponent
{
    protected $inlineAdd = true;
    protected $inlineEdit = true;

    protected function modifyList(DataGrid $grid) : DataGrid
    {
        $grid->addColumnText('name', 'list.column.name')
            ->setSortable();
        $grid->addColumnText('description', 'list.column.description');

        return $grid;
    }

    public function modifyInlineForm(\Nette\Forms\Container $container) : void
    {
        $container->addText('name')
            ->setRequired();
        $container->addTextArea('description');
    }
}
