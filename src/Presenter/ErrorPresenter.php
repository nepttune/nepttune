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

final class ErrorPresenter extends \Nepttune\Presenter\BasePresenter
{
    /** @var  \Nepttune\Model\ErrorLogModel */
    protected $errorLogModel;

    public function __construct(\Nepttune\Model\ErrorLogModel $errorLogModel)
    {
        $this->errorLogModel = $errorLogModel;
    }

    public function actionDefault($exception)
    {
        $this->errorLogModel->insert([
            'datetime' => new \Nette\Utils\DateTime(),
            'return_code' => $exception->getCode(),
            'ip_address' => inet_pton($this->getHttpRequest()->getRemoteAddress()),
            'url' => $this->getHttpRequest()->getUrl()
        ]);

        $code = $exception->getCode() >= 400 ? $exception->getCode() : 500;
        
        $this->template->code = $code;

        if ($code < 500)
        {
            $this->template->msg = 'Invalid request';
        }
        else
        {
            $this->template->msg = 'Internal error';
        }
    }
}
