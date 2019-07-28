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

namespace Nepttune\Model;

abstract class BaseTable implements IBaseRepository
{
    use \Nette\SmartObject;
    
    const TABLE_NAME = '';
    
    /** @var \Nette\Database\Context */
    protected $context;

    public function __construct(\Nette\Database\Context $context)
    {
        $this->context = $context;
    }

    public function getName() : string
    {
        return static::TABLE_NAME;
    }

    public function findAll() : \Nette\Database\Table\Selection
    {
        return $this->context->table(static::TABLE_NAME);
    }

    public function findBy(string $column, $value) : \Nette\Database\Table\Selection
    {
        return $this->findAll()->where($column, $value);
    }

    public function findByArray(array $filter) : \Nette\Database\Table\Selection
    {
        return $this->findAll()->where($filter);
    }

    public function findActive() : \Nette\Database\Table\Selection
    {
        return $this->findAll()->where(static::TABLE_NAME . '.active', 1);
    }

    public function findRow(int $rowId) : \Nette\Database\Table\Selection
    {
        return $this->findAll()->wherePrimary($rowId);
    }

    public function getRow(int $rowId) : \Nette\Database\Table\ActiveRow
    {
        $row = $this->findRow($rowId)->fetch();

        if (!$row instanceof \Nette\Database\Table\ActiveRow) {
            throw new \Nette\InvalidStateException('Row doesnt exist.');
        }

        return $row;
    }

    public function insert(array $data) : int
    {
        $row = $this->findAll()->insert($data);

        if (!$row instanceof \Nette\Database\Table\ActiveRow) {
            throw new \Nette\InvalidStateException('Insert has failed.');
        }

        return $row->id;
    }

    public function insertMany(array $data) : int
    {
        $result = $this->findAll()->insert($data);

        if ($result instanceof \Nette\Database\Table\ActiveRow) {
            return 1;
        }

        if (\is_int($result)) {
            return $result;
        }

        throw new \Nette\InvalidStateException('Insert has failed.');
    }

    public function update(int $rowId, array $data) : int
    {
        $row = $this->findRow($rowId);
        $row->update($data);

        return $rowId;
    }

    public function updateByArray(array $filter, array $data) : int
    {
        $rows = $this->findByArray($filter);
        $rows->update($data);

        return $rows->count('*');
    }

    public function upsert(?int $id, array $values) : int
    {
        if (\is_int($id)) {
            $this->update($id, $values);

            return $id;
        }

        return $this->insert($values);
    }

    public function delete(int $rowId) : void
    {
        $this->findRow($rowId)->delete();
    }

    public function deleteByArray(array $filter) : void
    {
        $this->findByArray($filter)->delete();
    }
    
    public function setActive(int $rowId, int $active): void
    {
        $this->findRow($rowId)->update(['active' => $active]);
    }

    public function count() : int
    {
        return $this->findAll()->count('*');
    }
    
    public function pairs($key, $value) : array
    {
        return $this->findAll()->fetchPairs($key, $value);
    }

    public function query(string $sql, ...$params) : \Nette\Database\ResultSet
    {
        return $this->context->query($sql, ...$params);
    }

    public function randomUniqueString(string $column, int $length = 5) : string
    {
        $table = static::TABLE_NAME;

        /** @noinspection PhpStrictTypeCheckingInspection */
        return $this->query(
            "SELECT `random` FROM (SELECT SUBSTRING(MD5(RAND()) FROM 1 FOR {$length}) AS `random`) AS `random`
             WHERE `random` NOT IN (SELECT `{$column}` FROM `{$table}` WHERE `{$column}` IS NOT NULL) LIMIT 1"
        )->fetch()->random;
    }

    public function randomUniqueNumber(string $column, int $length = 5) : int
    {
        $table = static::TABLE_NAME;

        $min = (int) (10 ** ($length - 1));
        $decimals = '8' . str_repeat('9', $length - 1);

        /** @noinspection PhpStrictTypeCheckingInspection */
        return $this->query(
            "SELECT `random` FROM (SELECT {$min} + FLOOR(RAND() * {$decimals}) AS `random`) AS `random`
             WHERE `random` NOT IN (SELECT `{$column}` FROM `{$table}` WHERE `{$column}` IS NOT NULL) LIMIT 1"
        )->fetch()->random;
    }
    
    public function getStructure() : array
    {
        return $this->context->getStructure()->getColumns(static::TABLE_NAME);
    }
    
    protected static $transactionLevel = 0;

    /**
     * @param callable $function
     * @return mixed
     */
    public function transaction(callable $function)
    {
        try
        {
            if (static::$transactionLevel === 0) {
                $this->context->beginTransaction();
            }

            static::$transactionLevel++;
            $result = $function();
            static::$transactionLevel--;

            if (static::$transactionLevel === 0) {
                $this->context->commit();
            }

            return $result;
        }
        catch (\PDOException $e)
        {
            if (static::$transactionLevel > 0) {
                $this->context->rollBack();
            }

            static::$transactionLevel = 0;
            throw $e;
        }
    }
}
