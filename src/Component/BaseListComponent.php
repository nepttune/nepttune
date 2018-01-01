<?php

namespace Peldax\NetteInit\Component;

use \Ublaboo\DataGrid\DataGrid;

abstract class BaseListComponent extends BaseComponent
{
    const ACTIVE = 1;

    /** @var  \Peldax\NetteInit\Model\BaseModel */
    protected $repository;

    public function render() : void
    {
        $this->beforeRender();
        $this->template->setFile(__DIR__. '/BaseListComponent.php');
        $this->template->render();
    }

    protected function createComponentList() : DataGrid
    {
        $grid = new DataGrid();
        $grid->setDataSource($this->getDataSource());

        return $this->modifyList($grid);
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
        $this['grid']->redrawItem($id);
    }

    public function inlineAdd($values) : void
    {
        $this->repository->save($values);
        $this['grid']->redrawControl();
    }

    public function inlineEdit(int $id, $values) : void
    {
        $values['id'] = $id;
        $this->repository->save($values);
        $this['grid']->redrawItem($id);
    }

    public function setDefaults($container, $item) : void
    {
        $container->setDefaults($item);
    }

    public function handleDelete(int $id) : void
    {
        $this->repository->findRow($id)->update(['active' => -1]);
        $this['grid']->redrawControl();
    }
}
