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
    public Response $response;
    public array $routes = [];

    /**
     * Router constructor.
     *
     * @param Request $request
     * @param Response $response
     */
    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function get(string $path, $callback)
    {
        $this->routes['get'][$path] = $callback;
    }

    public function post(string $path, $callback)
    {
        $this->routes['post'][$path] = $callback;
    }

    public function resolve()
    {
        $path = $this->request->getPath();
        $method = $this->request->getMethod();
        $callback = $this->routes[$method][$path] ?? false;
        if ($callback === false) {
            $this->response->setStatusCode(404);
            return $this->renderView("_404");
        }
        if (is_string($callback)) {
            return $this->renderView($callback);
        }
        if (is_array($callback)) {
            $callback[0] = new $callback[0](); // create instance of passed controller
        }
        return call_user_func($callback);
    }

    public function renderView(string $view, array $params = [])
    {
        $layoutContent = $this->layoutContent();
        $viewContent = $this->renderOnlyView($view, $params);
        return str_replace('{{content}}', $viewContent, $layoutContent);
    }

    public function renderContent(string $viewContent)
    {
        $layoutContent = $this->layoutContent();
        return str_replace('{{content}}', $viewContent, $layoutContent);
    }

    protected function layoutContent()
    {
        ob_start(); // This will stop everything from being displays but still buffers it
        include_once App::$ROOT_DIR . "/views/layouts/main.php";
        return ob_get_clean(); // Returns the content of the "display" buffer
    }

    protected function renderOnlyView(string $view, array $params)
    {
        foreach ($params as $param => $value) {
            $$param = $value; // If $param can be used as a variable name, then created one and fill it with the value
        }
        ob_start(); // This will stop everything from being displays but still buffers it
        include_once App::$ROOT_DIR . "/views/$view.php";
        return ob_get_clean(); // Returns the content of the "display" buffer
    }
}