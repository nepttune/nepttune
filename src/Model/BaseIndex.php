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

        return $this->index->getType('_doc')->search($query);
    }
    
    public function insert($id = '', array $data = []) : void
    {
        $this->createIndex();

        $doc = new \Elastica\Document($id, $data);
        $routing = $this->getRouting();

        if ($routing !== null) {
            $doc->setRouting($routing);
        }

        $this->index->getType('_doc')->addDocument($doc);
    }

    public function update($id, array $data) : void
    {
        $this->createIndex();

        $doc = new \Elastica\Document($id, $data);
        $routing = $this->getRouting();

        if ($routing !== null) {
            $doc->setRouting($routing);
        }

        $this->index->getType('_doc')->updateDocument($doc);
    }
    
    public function upsert($id, array $data) : void
    {
        $this->createIndex();

        $doc = new \Elastica\Document($id, $data);
        $doc->setDocAsUpsert(true);
        $routing = $this->getRouting();

        if ($routing !== null) {
            $doc->setRouting($routing);
        }

        $this->index->getType('_doc')->updateDocument($doc);
    }

    public function delete($id) : void
    {
        $this->createIndex();

        $doc = new \Elastica\Document($id);
        $routing = $this->getRouting();

        if ($routing !== null) {
            $doc->setRouting($routing);
        }

        try {
            $this->index->getType('_doc')->deleteDocument($doc);
        } catch (\Elastica\Exception\NotFoundException $e) {
            return;
        }
    }

    protected function createIndex() : void
    {
        if ($this->index->exists()) {
            return;
        }
        
        $this->index->create(static::PROPERTIES);

        $doc = $this->index->getType('_doc');
        $docMapping = new \Elastica\Type\Mapping($doc, static::MAPPING);
        $doc->setMapping($docMapping);

        $this->index->refresh();
    }

    protected function getRouting()
    {
        return null;
    }
}
