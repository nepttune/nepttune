<?php

namespace Peldax\NetteInit\Component;

final class LoginForm extends BaseComponent
{
    /** @var  \Peldax\NetteInit\Model\UserModel */
    protected $logLoginModel;

    public function createComponentForm()
    {
        $form = new \Nette\Application\UI\Form();

        $form->setRenderer(new \Nextras\Forms\Rendering\Bs3FormRenderer());

        $form->addProtection('Security token has expired, please submit the form again');
        $form->addText('username', 'Username')->setRequired();
        $form->addPassword('password', 'Password')->setRequired();

        $form->addSubmit('submit', 'Uložit');
        $form->onSuccess[] = [$this, 'formSubmitted'];

        return $form;
    }

    public function formSubmitted(\Nette\Application\UI\Form $form, \stdClass $values)
    {
        try
        {
            $this->getPresenter()->user->login($values->username, $values->password);
            $this->getPresenter()->user->setExpiration(0, TRUE);
        }
        catch (\Nette\Security\AuthenticationException $e)
        {
            return $this->getPresenter()->flashMessage($e->getMessage());
        }

        $this->getPresenter()->flashMessage('Successfully logged in.', 'success');
        $this->getPresenter()->redirect('Default:default');
    }
}