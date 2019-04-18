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

    /** @var \Nette\Database\Context */
    protected $client;

    public function __construct(\Kdyby\ElasticSearch\Client $client)
    {
        $this->client = $client;
    }

    public function search(array $query) : \Elastica\ResultSet
    {
        $index = $this->client->getIndex(static::INDEX_NAME);

        if (!$index->exists())
        {
            $index = $this->createIndex();
        }

        $type = $index->getType('doc');

        return $type->search($query);
    }

    public function createIndex() : \Elastica\Index
    {
        $index = $this->client->getIndex(static::INDEX_NAME);
        $index->create(static::PROPERTIES);

        $doc = $index->getType('doc');
        $docMapping = new \Elastica\Type\Mapping($doc, static::MAPPING);
        $doc->setMapping($docMapping);

        $index->refresh();
        return $index;
    }
}
