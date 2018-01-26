<?php

namespace Nepttune\Component;

use \Nette\Application\UI\Form;

final class RoleForm extends BaseFormComponent
{
    public function __construct(\Nepttune\Model\RoleModel $roleModel)
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
