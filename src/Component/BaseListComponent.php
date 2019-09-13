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

abstract class BaseListComponent extends BaseComponent implements \Nepttune\TI\ITranslator
{
    use \Nepttune\TI\TTranslator;

    protected const TEMPLATE_PATH = __DIR__. '/BaseListComponent.latte';
    
    protected const ACTIVE = true;
    protected const ACTIVE_FILTER = false;
    protected const SORT = ['active' => 'DESC'];
    protected const FILTER = [];
    protected const PER_PAGE = [10 => '10', 20 => '20', 50 => '50', 100 => '100'];
    protected const PER_PAGE_DEFAULT = 10;
    protected const PER_PAGE_ALL_LIMIT = 150;

    /** @var bool|string */
    protected $add = false;

    /** @var bool|string */
    protected $edit = false;

    /** @var bool|string */
    protected $delete = true;

    /** @var bool */
    protected $inlineAdd = false;

    /** @var bool */
    protected $inlineEdit = false;

    /** @var bool */
    protected $activeSwitch = true;

    /** @var  \Nepttune\Model\IBaseRepository */
    protected $repository;

    public function __construct(\Nepttune\Model\IBaseRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getDataSource()
    {
        $all = $this->repository->findAll();

        if (static::ACTIVE) {
            $activeCond = $this->activeSwitch ? 0 : 1;
            $all->where($this->repository->getName()  . '.active >= ?', $activeCond);
        }

        return $all;
    }

    public function statusChange($id, $status, string $column = 'active') : void
    {
        $id = (int) $id;
        $status = (int) $status;

        if (!\in_array($status, [0, 1], true)) {
            throw new \Nette\Application\BadRequestException();
        }

        \call_user_func([$this->repository, 'set' . \ucfirst($column)], $id, $status);
        $this['list']->redrawItem($id);
    }

    public function handleDelete($id) : void
    {
        $id = (int) $id;

        if (static::ACTIVE) {
            $this->repository->setActive($id, -1);
        }
        else {
            $this->repository->delete($id);
        }
        
        $this['list']->redrawControl();
    }

    public function saveInlineAdd(\stdClass $values) : void
    {
        $this->repository->insert((array) $values);
        $this['list']->redrawControl();
    }

    public function saveInlineEdit($id, \stdClass $values) : void
    {
        $id = (int) $id;

        $this->repository->update($id, (array) $values);
        $this['list']->redrawItem($id);
    }

    public function setInlineDefaults(\Nette\Forms\Container $container, $item) : void
    {
        $container->setDefaults($item);
    }

    public function modifyInlineForm(\Nette\Forms\Container $container) : void
    {

    }

    protected function createComponentList() : DataGrid
    {
        $grid = $this->createList();
        $grid = $this->modifyList($grid);

        if (static::ACTIVE && $this->activeSwitch) {
            $grid->addColumnStatus('active', 'list.column.active')
                ->setSortable()
                ->addOption(1, 'global.yes')
                ->endOption()
                ->addOption(0, 'global.no')
                ->setClass('btn-danger')
                ->endOption()
                ->onChange[] = [$this, 'statusChange'];

            if (static::ACTIVE_FILTER) {
                $grid->addFilterSelect('active', 'list.column.active', [
                    '' => $this->translator->translate('global.all'),
                    1 => $this->translator->translate('global.yes'),
                    0 => $this->translator->translate('global.no')
                ]);
            }
        }

        if ($this->inlineAdd) {
            $grid->addInlineAdd()
                ->setTitle('global.add')
                ->setClass('btn btn-md btn-primary ajax')
                ->onControlAdd[] = [$this, 'modifyInlineForm'];
            $grid->getInlineAdd()->onSubmit[] = [$this, 'saveInlineAdd'];
        }

        if ($this->inlineEdit) {
            $grid->addInlineEdit()
                ->setIcon('pencil-alt')
                ->setTitle('global.edit')
                ->setClass('btn btn-xs btn-primary ajax')
                ->onControlAdd[] = [$this, 'modifyInlineForm'];
            $grid->getInlineEdit()->onSubmit[] = [$this, 'saveInlineEdit'];
            $grid->getInlineEdit()->onSetDefaults[] = [$this, 'setInlineDefaults'];
        }

        if ($this->add) {
            $grid->addToolbarButton($this->add === true ? ':add' : $this->add, 'global.add')
                ->setIcon('plus')
                ->setClass('btn btn-md btn-primary');
        }

        if ($this->edit) {
            $grid->addAction('edit', '', $this->edit === true ? ':edit' : $this->edit, ['id' => 'id'])
                ->setIcon('pencil-alt')
                ->setTitle('global.edit')
                ->setClass('btn btn-xs btn-primary');
        }

        if ($this->delete) {
            $grid->addAction('delete', '', $this->delete === true ? 'delete!' : $this->delete)
                ->setIcon('trash-alt')
                ->setTitle('global.delete')
                ->setClass('btn btn-xs btn-danger' . ($this->delete === true ? ' ajax' : ''))
                ->setConfirmation(new \Ublaboo\DataGrid\Column\Action\Confirmation\StringConfirmation('global.confirm.delete'));
        }

        if (static::SORT) {
            $grid->setDefaultSort(static::SORT);
        }

        if (static::FILTER) {
            $grid->setDefaultFilter(static::FILTER);
        }

        return $grid;
    }

    abstract protected function modifyList(DataGrid $grid) : DataGrid;

    protected function createList() : DataGrid
    {
        $grid = new DataGrid();
        $grid->setRememberState(false);
        $grid->setDefaultPerPage(static::PER_PAGE_DEFAULT);
        $grid->setTranslator($this->translator);

        $grid->setDataSource($this->getDataSource());
        $grid->setItemsPerPageList(static::PER_PAGE, $grid->getDataSource()->getCount() <= static::PER_PAGE_ALL_LIMIT);

        return $grid;
    }
}
