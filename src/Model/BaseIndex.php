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

abstract class BaseIndex
{
    use \Nette\SmartObject;

    /** @var \Elastica\Index */
    protected $index;

    public function __construct(\Kdyby\ElasticSearch\Client $client)
    {
        $this->index = $client->getIndex(static::INDEX_NAME);
    }

    public function search(array $query) : \Elastica\ResultSet
    {
        if (!$this->index->exists()) {
            $this->createIndex();
        }

        return $this->index->getType('doc')->search($query);
    }

    public function createIndex() : void
    {
        $this->index->create(static::PROPERTIES);

        $doc = $this->index->getType('doc');
        $docMapping = new \Elastica\Type\Mapping($doc, static::MAPPING);
        $doc->setMapping($docMapping);

        $this->index->refresh();
    }
}
