<?php

namespace Okami\Core\Form;

/**
 * Class TextareaField
 *
 * @author Michal Tuček <michaltk1@gmail.com>
 * @package Okami\Core\Form
 */
class TextareaField extends BaseField
{
    public function renderInput(): string
    {
        return sprintf('<textarea name="%s" class="form-control%s">%s</textarea>',
            $this->attribute,
            $this->model->hasError($this->attribute) ? ' is-invalid' : '',
            $this->model->{$this->attribute}
        );
    }
}