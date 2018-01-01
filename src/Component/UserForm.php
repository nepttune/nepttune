<?php

namespace Peldax\NetteInit\Component;

use \Nette\Application\UI\Form;

final class UserForm extends BaseFormComponent
{
    public function __construct(\Peldax\NetteInit\Model\UserModel $userModel)
    {
        $this->repository = $userModel;
    }

    protected function modifyForm(Form $form) : Form
    {
        $form->addText('username', 'Uživatelské jméno')
            ->setRequired();
        $form->addPassword('password', 'Heslo')
            ->setRequired();
        $form->addPassword('password2', 'Heslo znovu')
            ->setRequired()
            ->addCondition($form::EQUAL, $form['password']);

        return $form;
    }

    public function formSuccess(\Nette\Application\UI\Form $form, \stdClass $values) : void
    {
        unset($values->password2);
        $values->registered = new \Nette\DateTime();
        $values->password = \Nette\Security\Passwords::hash($values->password);

        parent::formSuccess($values);
    }
}
