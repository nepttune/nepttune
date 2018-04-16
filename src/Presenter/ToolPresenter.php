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

namespace App\Presenter;

final class ToolPresenter extends \Nepttune\Presenter\BaseApiPresenter implements \Nepttune\Presenter\ILink
{
    use \Nepttune\Presenter\TLink;
    use \Nepttune\Presenter\TTemplate;
    
    /** @var array */
    protected $robots;

    public function __construct(array $robots)
    {
        $this->robots = $robots;
    }
    
    public function startup()
    {
        parent::startup();
        $this->getTemplate();
    }

    public function actionRobots()
    {
        $this->getHttpResponse()->setContentType('text/plain');

        $this->template->robots = $this->robots;
        
        $this->sendTemplate();
    }

    public function actionSitemap()
    {
        $this->getHttpResponse()->setContentType('application/xml');

        $cache = new \Nette\Caching\Cache($this->cacheStorage);

        $this->template->pages = $cache->call([$this, 'getPages']);
        $this->template->date = new \Nette\Utils\DateTime();

        $this->sendTemplate();
    }

    public function actionWorker()
    {
        $this->getHttpResponse()->addHeader('Service-Worker-Allowed', '/');
        $this->getHttpResponse()->setContentType('application/javascript');
        
        $this->sendTemplate();
    }

    public function getPages() : array
    {
        $pages = [];

        foreach ($this->context->findByType('\Nepttune\TI\ISitemap') as $name)
        {
            /** @var \Nepttune\TI\ISitemap $presenter */
            $presenter = $this->context->getService($name);
            $pages = array_merge($pages, $presenter->getSitemap());
        }

        return $pages;
    }
}
