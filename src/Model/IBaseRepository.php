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

interface IBaseRepository
{
    /**
     * Returns row from table by its id
     *
     * @param int $rowId
     * @return mixed
     */
    public function getRow(int $rowId);

    /**
     * Returns selection of all entries
     * 
     * @return mixed
     */
    public function findAll();// : \Countable;

    /**
     * Returns selection of entries found by associative array
     *
     * @param array $filter
     * @return mixed
     */
    public function findByArray(array $filter);// : \Countable;

    /**
     * Inserts data into table
     *
     * @param array $data
     * @return int
     */
    public function insert(array $data) : int;

    /**
     * Updates row
     *
     * @param int $rowId
     * @param array $data
     * @return int
     */
    public function update(int $rowId, array $data) : int;

    /**
     * Inserts data if no id provided, updates otherwise
     *
     * @param int $id
     * @param array $values
     * @return mixed
     */
    public function upsert(?int $id, array $values) : int;

    /**
     * Deletes row
     *
     * @param int $rowId
     */
    public function delete(int $rowId) : void;

    /**
     * Sets state
     *
     * @param int $rowId
     * @param int $active
     */
    public function setActive(int $rowId, int $active) : void;

    /**
     * Returns table name
     *
     * @return string
     */
    public function getName() : string;
}
