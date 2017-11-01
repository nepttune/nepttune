<?php

namespace App\Component;

final class UserForm extends BaseComponent
{
    /** @var  \App\Model\UserModel */
    protected $userModel;

    public function __construct(\App\Model\UserModel $userModel)
    {
        $this->userModel = $userModel;
    }

    public function createComponentForm()
    {
        $form = new \Nette\Application\UI\Form();

        $form->setRenderer(new \Nextras\Forms\Rendering\Bs3FormRenderer());

        $form->addProtection('Security token has expired, please submit the form again');
        $form->addText('username', 'Uživatelské jméno')
            ->setRequired();
        $form->addPassword('password', 'Heslo')
            ->setRequired();
        $form->addPassword('password2', 'Heslo znovu')
            ->setRequired()
            ->addCondition($form::EQUAL, $form['password']);

        $form->addSubmit('submit', 'Uložit');
        $form->onSuccess[] = [$this, 'formSubmitted'];

        return $form;
    }

    public function formSubmitted(\Nette\Application\UI\Form $form, \stdClass $values)
    {
        unset($values->password2);
        $values->registered = new \Nette\DateTime();
        $values->password = \Nette\Security\Passwords::hash($values->password);

        $this->userModel->save($values);

        $this->getPresenter()->redirect('User:default');
    }
}
