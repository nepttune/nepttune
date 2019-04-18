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

/**
 * Interface used by BaseFormComponent
 */
interface IFormRepository
{
    /**
     * Returns table name
     *
     * @return string
     */
    public function getName() : string;

    /**
     * Returns selection of entries found by associative array
     *
     * @param array $filter
     * @return mixed
     */
    public function findByArray(array $filter);

    /**
     * Returns row from table by its id
     *
     * @param int $rowId
     * @return mixed
     */
    public function getRow(int $rowId);

    /**
     * Inserts data into table
     *
     * @param array $data
     * @return mixed
     */
    public function insert(array $data);

    /**
     * Updates row
     *
     * @param int $rowId
     * @param array $data
     * @return mixed
     */
    public function update(int $rowId, array $data);
}
