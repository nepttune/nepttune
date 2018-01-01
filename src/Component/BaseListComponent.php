<?php

namespace Peldax\NetteInit\Component;

use \Ublaboo\DataGrid\DataGrid;

abstract class BaseListComponent extends BaseComponent
{
    const ACTIVE = 1;
    const INLINE = 1;
    const DELETE = 1;
    const SORT = ['active' => 'DESC'];

    /** @var  \Peldax\NetteInit\Model\BaseModel */
    protected $repository;

    public function render() : void
    {
        $this->beforeRender();
        $this->template->setFile(__DIR__. '/BaseListComponent.latte');
        $this->template->render();
    }

    protected function createComponentList() : DataGrid
    {
        $grid = new DataGrid();
        $grid->setDataSource($this->getDataSource());

        $grid = $this->modifyList($grid);

        if (static::ACTIVE)
        {
            $grid->addColumnStatus('active', 'Active')
                ->setSortable()
                ->addOption(1, 'Yes')
                ->endOption()
                ->addOption(0, 'No')
                ->endOption()
                ->onChange[] = [$this, 'statusChange'];
        }

        if (static::INLINE)
        {
            $grid->addInlineAdd()
                ->setTitle('Add')
                ->onControlAdd[] = [$this, 'modifyInlineForm'];
            $grid->getInlineAdd()->onSubmit[] = [$this, 'saveInlineAdd'];

            $grid->addInlineEdit()
                ->setTitle('Edit')
                ->onControlAdd[] = [$this, 'modifyInlineForm'];
            $grid->getInlineEdit()->onSubmit[] = [$this, 'saveInlineEdit'];
            $grid->getInlineEdit()->onSetDefaults[] = [$this, 'setInlineDefaults'];
        }

        if (static::DELETE)
        {
            $grid->addAction('delete', '', 'delete!')
                ->setIcon('trash')
                ->setTitle('Delete')
                ->setClass('btn btn-xs btn-danger ajax')
                ->setConfirm('Are you sure you want to delete this record?');
        }

        if (static::SORT)
        {
            $grid->setDefaultSort(static::SORT);
        }

        return $grid;
    }

    abstract protected function modifyList(DataGrid $grid) : DataGrid;

    public function getDataSource()
    {
        $all = $this->repository->getTable();

        if (static::ACTIVE)
        {
            return $all->where('active >= 0');
        }

        return $all;
    }

    public function statusChange(int $id, int $status) : void
    {
        if (!in_array($status, [0, 1], true))
        {
            throw new \Nette\Application\BadRequestException();
        }

        $this->repository->findRow($id)->update(['active' => $status]);
        $this['list']->redrawItem($id);
    }

    public function saveInlineAdd($values) : void
    {
        $this->repository->save($values);
        $this['list']->redrawControl();
    }

    public function saveInlineEdit(int $id, $values) : void
    {
        $values['id'] = $id;
        $this->repository->save($values);
        $this['list']->redrawItem($id);
    }

    public function modifyInlineForm(\Nette\Forms\Container $container) : void
    {

    }

    public function setInlineDefaults($container, $item) : void
    {
        $container->setDefaults($item);
    }

    public function handleDelete(int $id) : void
    {
        $this->repository->findRow($id)->update(['active' => -1]);
        $this['list']->redrawControl();
    }
}
