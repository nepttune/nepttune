<?php

namespace App\Presenter;

abstract class BaseAuthPresenter extends BasePresenter
{
    public function startup()
    {
        if (!$this->user->isLoggedIn())
        {
            $this->redirect(':Admin:Sign:in', ['backlink' => $this->storeRequest()]);
        }

        parent::startup();
    }

    public function beforeRender()
    {
        parent::beforeRender();

        $this->template->signOutDest = $this->context->parameters['signOutDest'];
        $this->template->userProfileDest = $this->context->parameters['userProfileDest'];
    }

    public static function getDefaultLayout() : string
    {
        return static::getAdminLayout();
    }

    public function useNotifications() : bool
    {
        return $this->context->hasService('notifications');
    }

    public function useUserDetail() : bool
    {
        return $this->context->hasService('userDetail');
    }

    public function useSidebar() : bool
    {
        return $this->context->hasService('sidebar');
    }

    public function useSearch() : bool
    {
        return $this->context->hasService('search');
    }
}
