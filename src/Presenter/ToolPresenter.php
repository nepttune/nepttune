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
    /** @var  \Nepttune\Component\ISitemapFactory */
    protected $iSitemapFactory;

    /** @var  \Nepttune\Component\IRobotsFactory */
    protected $iRobotsFactory;

    public function __construct(
        \Nepttune\Component\ISitemapFactory $ISitemapFactory,
        \Nepttune\Component\IRobotsFactory $IRobotsFactory)
    {
        $this->iSitemapFactory = $ISitemapFactory;
        $this->iRobotsFactory = $IRobotsFactory;
    }

    public function actionWorker()
    {
        $this->getHttpResponse()->addHeader('Service-Worker-Allowed', '/');
        $this->getHttpResponse()->setContentType('application/javascript');
    }

    protected function createComponentSitemap()
    {
        return $this->iSitemapFactory->create();
    }

    protected function createComponentRobots()
    {
        return $this->iRobotsFactory->create();
    }
}
