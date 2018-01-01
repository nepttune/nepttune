<?php

namespace Peldax\NetteInit\Component;

use \Nette\Application\UI\Form;

final class LoginForm extends BaseFormComponent
{
    /** @var  \Peldax\NetteInit\Model\LoginLogModel */
    protected $logLoginModel;

    protected function modifyForm(Form $form) : Form
    {
        $form->addText('username', 'Username')->setRequired();
        $form->addPassword('password', 'Password')->setRequired();

        return $form;
    }

    public function formSuccess(\Nette\Application\UI\Form $form, \stdClass $values) : void
    {
        try
        {
            $this->getPresenter()->getUser()->login($values->username, $values->password);
            $this->getPresenter()->getUser()->setExpiration(0, TRUE);
        }
        catch (\Nette\Security\AuthenticationException $e)
        {
            $this->getPresenter()->flashMessage($e->getMessage(), 'danger');
            return;
        }

        $this->getPresenter()->flashMessage('Successfully logged in.', 'success');

        $this->getPresenter()->restoreRequest($this->getPresenter()->getParameter('backlink'));
        $this->getPresenter()->redirect($this->getPresenter()->context->parameters['signInRedirect']);
    }
}
