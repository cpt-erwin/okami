<?php

namespace Okami\Core;

/**
 * Class Controller
 *
 * @author Michal Tuček <michaltk1@gmail.com>
 * @package Okami\Core
 */
class Controller
{
    public function render(string $view, array $params = [])
    {
        return App::$app->router->renderView($view, $params);
    }
}