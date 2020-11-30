<?php

namespace Okami\Core;

/**
 * Class Router
 *
 * @author Michal Tuček <michaltk1@gmail.com>
 * @package Okami\Core
 */
class Router
{
    public Request $request;
    public array $routes = [];

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function get($path, $callback)
    {
        $this->routes['get'][$path] = $callback;
    }

    public function resolve()
    {
        $path = $this->request->getPath();
        $method = $this->request->getMethod();
        $callback = $this->routes[$method][$path] ?? false;
        if ($callback === false) {
            App::$app->response->setStatusCode(404);
            return "Not found!";
        }
        if (is_string($callback)) {
            return $this->renderView($callback);
        }
        return call_user_func($callback);
    }

    public function renderView(string $view)
    {
        $layoutContent = $this->layoutContent();
        $viewContent = $this->renderOnlyView($view);
        return str_replace('{{content}}', $viewContent, $layoutContent);
    }

    protected function layoutContent()
    {
        ob_start(); // This will stop everything from being displays but still buffers it
        include_once App::$ROOT_DIR . "/views/layouts/main.php";
        return ob_get_clean(); // Returns the content of the "display" buffer
    }

    protected function renderOnlyView(string $view)
    {
        ob_start(); // This will stop everything from being displays but still buffers it
        include_once App::$ROOT_DIR . "/views/$view.php";
        return ob_get_clean(); // Returns the content of the "display" buffer
    }
}