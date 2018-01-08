<?php

namespace Peldax\NetteInit\Presenter;

abstract class ErrorPresenter extends \Peldax\NetteInit\Presenter\BasePresenter
{
    /** @var  \Peldax\NetteInit\Model\ErrorLogModel */
    protected $errorLogModel;

    public function actionDefault($exception, $request)
    {
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

    public function renderDefault()
    {
        $this->template->setFile(__DIR__ . '/../templates/Error/default.latte');
    }
}
