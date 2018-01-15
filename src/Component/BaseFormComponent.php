<?php

namespace Peldax\NetteInit\Component;

use \Nette\Application\UI\Form;

abstract class BaseFormComponent extends BaseComponent
{
    const REDIRECT = ':default';

    /** @var  \Peldax\NetteInit\Model\BaseModel */
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

        return $form;
    }

    abstract protected function modifyForm(Form $form) : Form;

    public function formSuccess(Form $form, \stdClass $values) : void
    {
        $this->repository->save($values);

        $this->getPresenter()->flashMessage($this->translator->translate('global.flash.save_success'), 'success');
        $this->getPresenter()->redirect(static::REDIRECT);
    }

    public function injectTranslator(\Kdyby\Translation\Translator $translator)
    {
        $this->translator = $translator;
    }
}
