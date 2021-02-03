<?php

namespace Okami\Core;

/**
 * Class Controller
 *
 * @author Michal Tuček <michaltk1@gmail.com>
 * @package Okami\Core
 */
abstract class Controller
{
    /**
     * @var string
     */
    public string $action = '';

    /**
     * @var string
     */
    private string $layout = 'main';

    /**
     * @param string $view
     * @param array $params
     *
     * @return string|string[]
     */
    public function render(string $view, array $params = [])
    {
        return App::$app->view->renderView($view, $params);
    }

    /**
     * @return string
     */
    public function getLayout(): string
    {
        return $this->layout;
    }

    /**
     * @param string $layout
     */
    public function setLayout(string $layout)
    {
        $this->layout = $layout;
    }
}