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

    /** @var \Nette\Http\Request */
    protected $request;

    public function __construct(
        \Nepttune\Model\ErrorLogModel $errorLogModel,
        \Nette\Http\Request $request)
    {
        $this->errorLogModel = $errorLogModel;
        $this->request = $request;
    }

    public function actionDefault($exception, $request)
    {
        $this->errorLogModel->insert([
            'datetime' => new \Nette\Utils\DateTime(),
            'return_code' => $exception->getCode(),
            'ip_address' => inet_pton($this->request->getRemoteAddress()),
            'url' => $this->request->getUrl()
        ]);
        
        $this->template->code = $exception->getCode();

        if ($exception->getCode() >= 500)
        {
            $this->template->msg = 'Internal error';
        }
        if ($exception->getCode() >= 400)
        {
            $this->template->msg = 'Invalid request';
        }
    }
}
