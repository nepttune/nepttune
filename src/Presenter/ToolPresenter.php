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
    
    /** @var \Nepttune\Model\PushNotificationModel */
    protected $pushNotificationModel;

    public function __construct(
        \Nepttune\Component\ISitemapFactory $ISitemapFactory,
        \Nepttune\Component\IRobotsFactory $IRobotsFactory,
        \Nepttune\Model\PushNotificationModel $pushNotificationModel)
    {
        parent::__construct();
        
        $this->iSitemapFactory = $ISitemapFactory;
        $this->iRobotsFactory = $IRobotsFactory;
        $this->pushNotificationModel = $pushNotificationModel;
    }

    public function actionWorker() : void
    {
        $this->getHttpResponse()->addHeader('Service-Worker-Allowed', '/');
    }
    
    public function actionSubscribe() : void
    {
        $this->pushNotificationModel->saveSubscription();
    }

    protected function createComponentSitemap() : \Nepttune\Component\Sitemap
    {
        return $this->iSitemapFactory->create();
    }

    protected function createComponentRobots() : \Nepttune\Component\Robots
    {
        return $this->iRobotsFactory->create();
    }
}
