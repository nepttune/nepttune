<?php

namespace Peldax\NetteInit\Component;

use \Nette\Application\UI\Form;

final class RoleForm extends BaseFormComponent
{
    public function __construct(\Peldax\NetteInit\Model\RoleModel $roleModel)
    {
        $this->repository = $roleModel;
    }

    protected function modifyForm(Form $form) : Form
    {
        $form->addText('name', 'Název role')
            ->setRequired();

        return $form;
    }
}
