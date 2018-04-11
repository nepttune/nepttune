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

final class SubscribePresenter extends \Nepttune\Presenter\BasePresenter
{
    /**
     * @inject
     * @var \Nepttune\Model\PushNotificationModel
     */
    public $pushNotificationModel;

    /** @var string */
    protected $publicKey;

    public function __construct(string $publicKey)
    {
        parent::__construct();

        $this->publicKey = $publicKey;
    }

    public function actionDefault()
    {
        $this->template->publicKey = $this->publicKey;
    }

    public function handleSubscribe()
    {
        $this->pushNotificationModel->saveSubscription();
    }
}
