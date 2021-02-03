<?php

namespace Okami\Core;

/**
 * Class View
 *
 * @author Michal Tuček <michaltk1@gmail.com>
 * @package Okami\Core
 */
class View
{
    /**
     * @var string
     */
    public string $title = '';

    /**
     * FIXME: Return type should be only string!
     *
     * @param string $view
     * @param array $params
     *
     * @return string|string[]
     */
    public function renderView(string $view, array $params = [])
    {
        $viewContent = $this->renderOnlyView($view, $params);
        $layoutContent = $this->layoutContent();

        return str_replace('{{content}}', $viewContent, $layoutContent);
    }

    /**
     * FIXME: Return type should be only string!
     *
     * @param string $view
     * @param array $params
     *
     * @return false|string
     */
    protected function renderOnlyView(string $view, array $params)
    {
        foreach ($params as $param => $value) {
            $$param = $value; // If $param can be used as a variable name, then created one and fill it with the value
        }
        ob_start(); // This will stop everything from being displays but still buffers it
        /** @noinspection PhpIncludeInspection */
        include_once App::$ROOT_DIR . "/views/$view.phtml";

        return ob_get_clean(); // Returns the content of the "display" buffer
    }

    /**
     * FIXME: Return type should be only string!
     *
     * @return false|string
     */
    protected function layoutContent()
    {
        $layout = App::$app->layout;
        if (App::$app->getController()) {
            $layout = App::$app->getController()->getLayout();
        }
        ob_start(); // This will stop everything from being displays but still buffers it
        /** @noinspection PhpIncludeInspection */
        include_once App::$ROOT_DIR . "/views/layouts/$layout.phtml";

        return ob_get_clean(); // Returns the content of the "display" buffer
    }

    /**
     * FIXME: Return type should be only string!
     *
     * @param string $viewContent
     *
     * @return false|string|string[]
     */
    public function renderContent(string $viewContent)
    {
        $layoutContent = $this->layoutContent();

        return str_replace('{{content}}', $viewContent, $layoutContent);
    }
}