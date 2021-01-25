<?php

namespace Okami\Core;

use Okami\Core\Exceptions\NotFoundException;

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
        $method = $this->request->method();
        $callback = $this->routes[$method][$path] ?? false;
        if ($callback === false) {
            throw new NotFoundException();
        }
        if (is_string($callback)) {
            return App::$app->view->renderView($callback);
        }
        if (is_array($callback)) {
            App::$app->setController(new $callback[0]()); // create instance of passed controller
            App::$app->controller->action = $callback[1];
            $callback[0] = App::$app->getController();

            foreach (App::$app->controller->getMiddlewares() as $middleware) {
                $middleware->execute();
            }
        }
        return call_user_func($callback, $this->request, $this->response);
    }
}