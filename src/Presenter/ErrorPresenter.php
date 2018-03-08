<?php

namespace Nepttune\Presenter;

final class ErrorPresenter extends \Nepttune\Presenter\BasePresenter
{
    /** @var  \Nepttune\Model\ErrorLogModel */
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
