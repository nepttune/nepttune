<?php

namespace Peldax\NetteInit\Component;

use \Ublaboo\DataGrid\DataGrid;

final class RoleList extends BaseListComponent
{
    public function __construct(\Peldax\NetteInit\Model\RoleModel $roleModel)
    {
        $this->repository = $roleModel;
    }

    protected function modifyList(DataGrid $grid) : DataGrid
    {
        $grid->addColumnText('username', 'Přihlašovací jméno')
            ->setSortable();
        $grid->addColumnStatus('active', 'Aktivní')
            ->setSortable()
            ->addOption(1, 'Ano')
            ->endOption()
            ->addOption(0, 'Ne')
            ->endOption()
            ->onChange[] = [$this, 'statusChange'];

        $grid->addAction('delete', '', 'delete!')
            ->setIcon('trash')
            ->setTitle('Smazat')
            ->setClass('btn btn-xs btn-danger ajax')
            ->setConfirm('Opravdu odstranit?');

        $grid->addToolbarButton(':Admin:User:add', 'Přidat');

        return $grid;
    }
}
