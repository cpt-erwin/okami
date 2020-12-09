<?php

namespace Okami\Core;

use Okami\Core\Middlewares\Middleware;

/**
 * Class Controller
 *
 * @author Michal Tuček <michaltk1@gmail.com>
 * @package Okami\Core
 */
class Controller
{
    private string $layout = 'main';

    /**
     * @var Middleware[]
     */
    public array $middlewares;

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

    public function registerMiddleware(Middleware $middleware): void
    {
        $this->middlewares[] = $middleware;
    }
}