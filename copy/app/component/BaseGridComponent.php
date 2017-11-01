<?php

namespace App\Component;

abstract class BaseGridComponent extends BaseComponent
{
    /** @var  \App\Model\BaseModel */
    protected $repository;

    public function getDataSource()
    {
        $all = $this->repository->getTable();

        $columns = $this->repository
            ->getConnection()
            ->getStructure()
            ->getColumns($this->repository->getTableName());

        if (end($columns)['name'] === 'active')
        {
            return $all->where('active >= 0');
        }

        return $all;
    }

    public function statusChange(int $id, int $status)
    {
        if (!in_array($status, [0, 1], true))
        {
            return;
        }

        $this->repository->findRow($id)->update(['active' => $status]);
        $this['grid']->redrawItem($id);
    }

    public function inlineAdd($values)
    {
        $this->repository->save($values);
        $this['grid']->redrawControl();
    }

    public function inlineEdit(int $id, $values)
    {
        $values['id'] = $id;
        $this->repository->save($values);
        $this['grid']->redrawItem($id);
    }

    public function setDefaults($container, $item)
    {
        $container->setDefaults($item);
    }

    public function handleDelete(int $id)
    {
        $this->repository->findRow($id)->update(['active' => -1]);
        $this['grid']->redrawControl();
    }
}
