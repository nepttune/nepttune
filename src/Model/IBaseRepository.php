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

interface IBaseRepository extends IListRepository
{
    /**
     * Returns selection of all entries
     * 
     * @return mixed
     */
    public function findAll();
    
    /**
     * Returns selection of entries by custom condition
     * 
     * @param string $column
     * @param $value
     * @return mixed
     */
    public function findBy(string $column, $value);

    /**
     * Returns selection of active entries
     * 
     * @return mixed
     */
    public function findActive();

    /**
     * Returns selection of entries found by id
     * 
     * @param int $rowId
     * @return mixed
     */
    public function findRow(int $rowId);

    /**
     * Inserts multiple rows
     * 
     * @param array $data
     * @return mixed
     */
    public function insertMany(array $data);

    /**
     * Updates entries found by associative array
     * 
     * @param array $filter
     * @param array $data
     * @return mixed
     */
    public function updateByArray(array $filter, array $data);

    /**
     * Inserts data if no id provided, updates otherwise
     * 
     * @param array $data
     * @return mixed
     */
    public function upsert(array $data);

    /**
     * Deletes entries fourd by associative array
     * 
     * @param array $filter
     */
    public function deleteByArray(array $filter) : void;

    /**
     * Returns count of entries
     * 
     * @return int
     */
    public function count() : int;

    /**
     * Performs custom query
     * 
     * @param string $query
     * @param mixed ...$params
     * @return mixed
     */
    public function query(string $query, ...$params);
}
