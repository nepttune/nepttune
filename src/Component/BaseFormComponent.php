<?php

namespace Nepttune\Component;

use \Nette\Application\UI\Form;

abstract class BaseFormComponent extends BaseComponent
{
    const SAVE_NEXT = 1;
    const SAVE_LIST = 1;

    const REDIRECT = ':default';
    const REDIRECT_ID = 0;
    const REDIRECT_NEXT = ':add';
    const REDIRECT_NEXT_ID = 0;
    const REDIRECT_LIST = ':list';
    const REDIRECT_LIST_ID = 0;
    
    const PATTERN_PHONE = '^[+(]{0,2}[0-9 ().-]{9,}';

    /** @var  \Nepttune\Model\BaseModel */
    protected $repository;

    /** @var  \Kdyby\Translation\Translator */
    protected $translator;

    public function render() : void
    {
        $this->beforeRender();
        $this->template->setFile(__DIR__. '/BaseFormComponent.latte');
        $this->template->render();
    }

    protected function createComponentForm() : Form
    {
        $form = new Form();

        $form->setTranslator($this->translator);
        $form->setRenderer(new \Nextras\Forms\Rendering\Bs3FormRenderer());
        $form->addProtection('form.error.csrf');

        $form = $this->modifyForm($form);

        $form->addSubmit('submit', 'global.save');
        $form->onSuccess[] = [$this, 'formSuccess'];

        if (static::SAVE_NEXT)
        {
            $form->addSubmit('save_next', 'global.save_next');
        }

        if (static::SAVE_LIST)
        {
            $form->addSubmit('save_list', 'global.save_list');
        }
        
        return $form;
    }

    abstract protected function modifyForm(Form $form) : Form;

    public function formSuccess(Form $form, \stdClass $values) : void
    {
        if ($this->getPresenter()->getAction() === 'edit')
        {
            $values->id = $this->getPresenter()->getId();
        }
        
        $row = $this->repository->save($values);

        $params = [];
        switch ($form->isSubmitted()->name)
        {
            case 'save_next':
                if (static::REDIRECT_NEXT_ID) { $params = ['id' => $row->id]; }
                $redirect = static::REDIRECT_NEXT;
                break;
            case 'save_list':
                if (static::REDIRECT_LIST_ID) { $params = ['id' => $row->id]; }
                $redirect = static::REDIRECT_LIST;
                break;
            default:
                if (static::REDIRECT_ID) { $params = ['id' => $row->id]; }
                $redirect = static::REDIRECT;
        }

        $this->getPresenter()->flashMessage($this->translator->translate('global.flash.save_success'), 'success');
        $this->getPresenter()->redirect($redirect, $params);
    }

    public function injectTranslator(\Kdyby\Translation\Translator $translator)
    {
        $this->translator = $translator;
    }
}
