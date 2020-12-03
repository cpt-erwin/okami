<?php

namespace Okami\Core\Form;

use Okami\Core\Model;

/**
 * Class Form
 *
 * @author Michal Tuček <michaltk1@gmail.com>
 * @package Okami\Core\Form
 */
class Form
{
    public static function begin(string $action, string $method)
    {
        echo sprintf('<form action="%s" method="%s">', $action, $method);
        return new Form();
    }

    public static function end()
    {
        return '</form>';
    }

    public function field(Model $model, string $attribute)
    {
        return new Field($model, $attribute);
    }
}