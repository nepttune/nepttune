<?php

namespace Peldax\NetteInit\Component;

class Breadcrumb extends BaseComponent
{
    public function render() : void
    {
        $module = $this->getPresenter()->getModule();
        $presenter = $this->getPresenter()->getNameWM();
        $action = $this->getPresenter()->getAction();

        $breadcrumbs = [];

        if (class_exists('\App\AppModule\Presenter\DefaultPresenter'))
        {
            $breadcrumbs[':App:Default:default'] = 'Home';
        }

        if ($module !== 'App')
        {
            $breadcrumbs['Default:default'] = $module;
        }

        if ($presenter !== 'Default')
        {
            $breadcrumbs[':default'] = $presenter;
        }

        if ($action !== 'default')
        {
            $breadcrumbs['X'] = ucfirst($action);
        }

        $this->template->breadcrumbs = $breadcrumbs;

        parent::render();
    }
}
