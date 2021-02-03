<?php

namespace Okami\Core\Form;

use Okami\Core\Model;

/**
 * Class BaseField
 *
 * @author Michal Tuček <michaltk1@gmail.com>
 * @package Okami\Core\Form
 */
abstract class BaseField
{
    public Model $model;

    public string $attribute;

    /**
     * Field constructor.
     *
     * @param Model $model
     * @param string $attribute
     */
    public function __construct(Model $model, string $attribute)
    {
        $this->model = $model;
        $this->attribute = $attribute;
    }

    public function __toString()
    {
        return sprintf('
            <div class="field is-horizontal">
                <div class="field-label is-normal">
                    <label class="label">%s</label>
                </div>
                <div class="field-body">
                    <div class="field">
                        <p class="control">
                            %s
                        </p>
                    </div>
                    <p class="help is-danger">%s</p>
                </div>
            </div>
        ',
            $this->model->getLabel($this->attribute),
            $this->renderInput(),
            $this->model->getFirstError($this->attribute)
        );
    }

    abstract public function renderInput(): string;
}