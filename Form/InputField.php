<?php

namespace Okami\Core\Form;

use Okami\Core\Model;

/**
 * Class Field
 *
 * @author Michal Tuček <michaltk1@gmail.com>
 * @package Okami\Core\Form
 */
class InputField extends BaseField
{
    public const TYPE_TEXT = 'text';
    public const TYPE_EMAIL = 'email';
    public const TYPE_PASSWORD = 'password';
    public const TYPE_NUMBER = 'number';

    public string $type;

    /**
     * Field constructor.
     *
     * @param Model $model
     * @param string $attribute
     */
    public function __construct(Model $model, string $attribute)
    {
        $this->type = self::TYPE_TEXT;
        parent::__construct($model, $attribute);
    }

    public function emailField()
    {
        $this->type = self::TYPE_EMAIL;

        return $this;
    }

    public function passwordField()
    {
        $this->type = self::TYPE_PASSWORD;

        return $this;
    }

    public function numberField()
    {
        $this->type = self::TYPE_NUMBER;

        return $this;
    }

    public function renderInput(): string
    {
        return sprintf('<input type="%s" name="%s" value="%s" class="input %s">',
            $this->type,
            $this->attribute,
            $this->model->{$this->attribute},
            $this->model->hasError($this->attribute) ? 'is-danger' : ''
        );
    }
}