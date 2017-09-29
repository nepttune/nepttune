<?php

namespace App\Model;

use Nette;

abstract class BaseModel extends Nette\Object
{
    /** @var string Table name */
    public $tableName;

    /** @var Nette\Database\Context */
    protected $context;

    public function __construct(Nette\Database\Context $context)
    {
        $this->context = $context;
    }

    public function getConnection() : Nette\Database\Context
    {
        return $this->context;
    }

    public function getTableName() : string
    {
        return $this->tableName;
    }

    public function getTable() : Nette\Database\Table\Selection
    {
        return $this->context->table($this->tableName);
    }

    public function findByArray(array $filter) : Nette\Database\Table\Selection
    {
        return $this->getTable()->where($filter);
    }

    public function findBy(string $column, mixed $value) : Nette\Database\Table\Selection
    {
        return $this->getTable()->where($column, $value);
    }

    public function findRow(int $rowId) : Nette\Database\Table\Selection
    {
        return $this->getTable()->wherePrimary($rowId);
    }

    public function query($sql, ...$params) : Nette\Database\ResultSet
    {
        return $this->context->query($sql, ...$params);
    }

    public function count() : int
    {
        return $this->getTable()->count();
    }

    public function insert($data) : Nette\Database\IRow
    {
        return $this->getTable()->insert($data);
    }

    public function delete($rowId)
    {
        $this->findRow($rowId)->delete();
    }

    public function save($data) : Nette\Database\IRow
    {
        if (isset($data['id']) && $data['id'])
        {
            $data['id'] = (int) $data['id'];
            $row = $this->findRow($data['id']);
            $row->update($data);
            return $row;
        }
        unset($data['id']);
        return $this->insert($data);
    }
}
