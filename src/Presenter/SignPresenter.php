<?php

namespace Peldax\NetteInit\Presenter;

abstract class SignPresenter extends BasePresenter
{
    /** @persistent */
    public $backlink;

    public function actionOut()
    {
        $this->user->logout();

        $this->flashMessage('Successfully logged out.', 'success');
        $this->redirect($this->context->parameters['redirectSignOut']);
    }

    public function renderIn()
    {
        $this->template->setFile(__DIR__ . '/../templates/Sign/in.latte');
    }
}
