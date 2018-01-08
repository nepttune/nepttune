<?php

namespace Peldax\NetteInit\Presenter;

abstract class BaseAuthPresenter extends BasePresenter
{
    protected function startup()
    {
        if (!$this->user->isLoggedIn())
        {
            $this->redirect($this->context->parameters['signInDest'], ['backlink' => $this->storeRequest()]);
        }

        parent::startup();
    }

    protected function beforeRender()
    {
        parent::beforeRender();

        $this->template->destSignOut = $this->context->parameters['destSignOut'];
        $this->template->destUserProfile = $this->context->parameters['destUserProfile'];

        $this->template->destAuthHomepage = $this->context->parameters['destAuthHomepage'];
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
