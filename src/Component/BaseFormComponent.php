<?php

namespace Peldax\NetteInit\Component;

use \Nette\Application\UI\Form;

abstract class BaseFormComponent extends BaseComponent
{
    const REDIRECT = ':default';
    const MESSAGE = 'Successfully saved.';

    /** @var  \Peldax\NetteInit\Model\BaseModel */
    protected $repository;

    public function render() : void
    {
        $this->beforeRender();
        $this->template->setFile(__DIR__. '/BaseFormComponent.php');
        $this->template->render();
    }

    protected function createComponentForm() : Form
    {
        $form = new Form();

        $form->setRenderer(new \Nextras\Forms\Rendering\Bs3FormRenderer());
        $form->addProtection('Security token has expired, please submit the form again');

        $form = $this->modifyForm($form);

        $form->addSubmit('submit', 'Uložit');
        $form->onSuccess[] = [$this, 'formSuccess'];

        return $form;
    }

    abstract protected function modifyForm(Form $form) : Form;

    public function formSuccess(Form $form, \stdClass $values) : void
    {
        $this->repository->save($values);

        $this->getPresenter()->flashMessage(static::MESSAGE, 'success');
        $this->getPresenter()->redirect(static::REDIRECT);
    }
}
