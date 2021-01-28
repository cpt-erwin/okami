<?php

namespace Okami\Core;

use Okami\Core\Middlewares\Middleware;

/**
 * Class Controller
 *
 * @author Michal Tuček <michaltk1@gmail.com>
 * @package Okami\Core
 */
abstract class Controller
{
    private string $layout = 'main';
    public string $action = '';

    public function render(string $view, array $params = [])
    {
        return App::$app->view->renderView($view, $params);
    }

    public function getLayout(): string
    {
        return $this->layout;
    }

    public function setLayout(string $layout)
    {
        $this->layout = $layout;
    }
}