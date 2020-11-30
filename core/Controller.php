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
    private string $layout = 'main';

    public function render(string $view, array $params = [])
    {
        return App::$app->router->renderView($view, $params);
    }

    public function getLayout(): string
    {
        return $this->layout;
    }

    public function setLayout(string $layout): void
    {
        $this->layout = $layout;
    }
}