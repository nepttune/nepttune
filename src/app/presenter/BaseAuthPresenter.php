<?php

namespace App\Presenter;

abstract class BaseAuthPresenter extends BasePresenter
{
    public function startup()
    {
        if (!$this->user->isLoggedIn() && $this->getName())
        {
            $this->redirect(':Admin:Sign:in', ['backlink' => $this->storeRequest()]);
        }

        parent::startup();
    }

    public static function getDefaultLayout() : string
    {
        return static::getAdminLayout();
    }
}
