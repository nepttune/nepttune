<?php

/**
 * This file is part of Nepttune (https://www.peldax.com)
 *
 * Copyright (c) 2018 Václav Pelíšek (info@peldax.com)
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license. For more information, see
 * <https://www.peldax.com>.
 */

declare(strict_types = 1);

namespace Nepttune\Component;

use \Nette\Application\UI\Form;

abstract class BaseFormComponent extends BaseComponent implements \Nepttune\TI\ITranslator
{
    use \Nepttune\TI\TTranslator;
    
    protected const TEMPLATE_PATH = __DIR__. '/BaseFormComponent.latte';
    protected const SAVE_NEXT = false;
    protected const SAVE_LIST = false;
    
    public const PATTERN_PHONE = '^[+(]{0,2}[0-9 ().-]{9,}$';
    public const PATTERN_FILE = '^[\w,\s-]+\.[A-Za-z]{3,}$';
    public const PATTERN_USERNAME = '^(?=.{4,20}$)(?![_.\-])(?!.*[_.\-]{2})[a-zA-Z0-9._\-]+(?<![_.])$';
    public const PATTERN_PASSWORD = '^[a-zA-Z0-9._!?@#%&\-\$\^\*]{8,50}$';
    
    public const VALIDATOR_UNIQUE = 'validateUnique';
    public const VALIDATOR_UNIQUE_MSG = 'form.error.unique';
    public const VALIDATOR_SAME_LENGTH = 'Nepttune\Validator\CoreValidator::sameLength';
    public const VALIDATOR_SAME_LENGTH_MSG = 'form.error.same_length';
    public const VALIDATOR_IS_IN = 'Nepttune\Validator\CoreValidator::isIn';
    public const VALIDATOR_IN_IN_MSG = 'form.error.is_in';
    
    /** @var \Nepttune\Model\IBaseRepository */
    protected $repository;
    
    /** @var int */
    protected $rowId;

    /** @var callable */
    public $saveCallback;

    /** @var callable */
    public $failureCallback;

    public function __construct(\Nepttune\Model\IBaseRepository $repository)
    {
        $this->repository = $repository;
    }

    public function setDefaults(int $rowId) : void
    {
        $this->rowId = $rowId;

        $this->setDuplicate($rowId);
    }
    
    public function setDuplicate(int $rowId) : void
    {
        $this['form']->setDefaults($this->repository->getRow($rowId));
    }

    public function setAjax() : void
    {
        $this['form']->getElementPrototype()->appendAttribute('class', 'ajax');
    }
    
    protected function createComponentForm() : Form
    {
        $form = new Form();
        $form->setTranslator($this->translator);
        $form->setRenderer(new \Nextras\Forms\Rendering\Bs3FormRenderer());
        $form->addProtection('form.error.csrf');
        
        $form = $this->modifyForm($form);
        
        if (static::SAVE_NEXT) {
            $form->addSubmit('save_next', 'global.save_next');
        }
        if (static::SAVE_LIST) {
            $form->addSubmit('save_list', 'global.save_list');
        }
        
        $submit = $form->addSubmit('submit', 'global.save');
        $form->getRenderer()->primaryButton = $submit;
        
        $form->onSuccess[] = [$this, 'formSuccess'];
        $form->onError[] = [$this, 'formError'];
        
        return $form;
    }
    
    abstract protected function modifyForm(Form $form) : Form;
    
    public function formSuccess(Form $form, \stdClass $values) : void
    {
        $values = \array_map(static function($value) {return $value === "" ? null : $value;}, (array) $values);

        $rowId = $this->repository->upsert($this->rowId, $values);

        if (\is_callable($this->saveCallback)) {
            \call_user_func($this->saveCallback, $form, $values, $rowId);
        }
    }

    public function formError(Form $form, string $msg = '') : void
    {
        if (\is_callable($this->failureCallback)) {
            \call_user_func($this->failureCallback, $form, $msg);
        }
    }

    public function validateUnique(\Nette\Forms\IControl $control)
    {
        $id = $this->rowId ?: 0;

        return \count($this->repository->findByArray([
                $control->getName() => $control->getValue(),
                'id != ?' => $id
            ])) === 0;
    }
}
