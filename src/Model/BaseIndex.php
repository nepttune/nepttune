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

    protected const INDEX_NAME = '';
    protected const PROPERTIES = [];
    protected const MAPPING = [];
    
    /** @var \Elastica\Index */
    protected $index;

    public function __construct(\Kdyby\ElasticSearch\Client $client)
    {
        $this->index = $client->getIndex(static::INDEX_NAME);
    }

    public function search(array $query) : \Elastica\ResultSet
    {
        $this->createIndex();

        return $this->index->getType('doc')->search($query);
    }
    
    public function insert($id = '', array $data = []) : void
    {
        $this->createIndex();
        $this->index->addDocuments([new \Elastica\Document($id, $data)]);
    }

    public function update($id, array $data) : void
    {
        $this->createIndex();
        $this->index->updateDocuments([new \Elastica\Document($id, $data)]);
    }
    
    public function upsert($id, array $data) : void
    {
        $this->createIndex();
        $doc = new \Elastica\Document($id, $data);
        $doc->setDocAsUpsert(true);
        $this->index->updateDocuments([$doc]);
    }

    public function delete($id) : void
    {
        $this->createIndex();
        $this->index->deleteDocuments([new \Elastica\Document($id)]);
    }

    protected function createIndex() : void
    {
        if ($this->index->exists()) {
            return;
        }
        
        $this->index->create(static::PROPERTIES);

        $doc = $this->index->getType('doc');
        $docMapping = new \Elastica\Type\Mapping($doc, static::MAPPING);
        $doc->setMapping($docMapping);

        $this->index->refresh();
    }
}
