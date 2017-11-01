<?php

namespace App\Component;

class UserList extends \App\Component\BaseGridComponent
{
    public function __construct(\App\Model\UserModel $userModel)
    {
        $this->repository = $userModel;
    }

    public function createComponentGrid()
    {
        $grid = new \Ublaboo\DataGrid\DataGrid();
        $grid->setDataSource($this->getDataSource());

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
