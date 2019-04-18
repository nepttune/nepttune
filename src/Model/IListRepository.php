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

interface IListRepository extends IFormRepository
{
    /**
     * Returns nette selection of all entries - for lists
     *
     * @return mixed
     */
    public function getNetteSelection() : \Nette\Database\Table\Selection;

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
}
