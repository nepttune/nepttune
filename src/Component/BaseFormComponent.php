<?php

namespace Nepttune\Component;

use \Nette\Application\UI\Form;

abstract class BaseFormComponent extends BaseComponent
{
    const REDIRECT = ':default';
    const REDIRECT_ID = false;

    const SAVE_NEXT = true;
    const SAVE_NEXT_REDIRECT = ':add';
    const SAVE_NEXT_ID = false;

    const SAVE_LIST = true;
    const SAVE_LIST_REDIRECT = ':list';
    const SAVE_LIST_ID = false;
    
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
                if (static::SAVE_NEXT_ID) { $params = ['id' => $row->id]; }
                $redirect = static::SAVE_NEXT_REDIRECT;
                break;
            case 'save_list':
                if (static::SAVE_LIST_ID) { $params = ['id' => $row->id]; }
                $redirect = static::SAVE_LIST_REDIRECT;
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
