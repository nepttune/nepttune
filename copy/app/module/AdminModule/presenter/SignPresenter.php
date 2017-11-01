<?php

namespace App\AdminModule\Presenter;

final class SignPresenter extends \Peldax\NetteInit\Presenter\BasePresenter
{
    public function actionOut()
    {
        $this->getUser()->logout();

        $this->flashMessage('Successfully logged out.', 'success');
        $this->redirect('Sign:in');
    }
}
