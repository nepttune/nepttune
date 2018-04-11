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

final class ToolPresenter extends \Nepttune\Presenter\BasePresenter
{
    /** @var array */
    protected $robots;

    public function __construct(array $robots)
    {
        parent::__construct();

        $this->robots = $robots;
    }

    public function actionRobots()
    {
        $this->getHttpResponse()->setContentType('text/plain');

        $this->template->robots = $this->robots;
    }

    public function actionSitemap()
    {
        $this->getHttpResponse()->setContentType('application/xml');

        $this->template->pages = $this->getPages();
        $this->template->date = new \Nette\Utils\DateTime();
    }

    public function actionWorker()
    {
        $this->getHttpResponse()->addHeader('Service-Worker-Allowed', '/');
        $this->getHttpResponse()->setContentType('application/javascript');
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
