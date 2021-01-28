<?php

namespace Okami\Core\Routing;

use LogicException;
use Okami\Core\App;
use Okami\Core\Exceptions\NotFoundException;
use Okami\Core\Request;
use Okami\Core\Response;

/**
 * Class Router
 *
 * @author Michal Tuček <michaltk1@gmail.com>
 * @package Okami\Core\Routing
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

    /**
     * @param string $path
     * @param string|callable|array $callback
     */
    public function get(string $path, $callback)
    {
        $this->addRoute('get', $path, $callback);
    }

    /**
     * @param string $path
     * @param string|callable|array $callback
     */
    public function post(string $path, $callback)
    {
        $this->addRoute('post', $path, $callback);
    }

    /**
     * @param string $method
     * @param string $path
     * @param string|callable|array $callback
     */
    private function addRoute(string $method, string $path, $callback)
    {
        $route = new Route($path, $callback);
        $this->routes[$method][] = $route;
    }

    public function resolve()
    {
        $path = $this->request->getPath();
        $method = $this->request->method();

        $route = $this->getRoute($method, $path);
        if (is_null($route)) {
            throw new NotFoundException();
        }

        /** RENDER TEMPLATE **/
        if (is_string($route->getCallback())) {
            return App::$app->view->renderView($route->getCallback());
        }

        /** CALL CONTROLLER **/
        if (is_array($route->getCallback())) {
            $callback = $route->getCallback();
            App::$app->setController(new $callback[0]()); // create instance of passed controller
            App::$app->controller->action = $callback[1];
            $callback[0] = App::$app->getController();

            foreach (App::$app->controller->getMiddlewares() as $middleware) {
                $middleware->execute();
            }
            return call_user_func($callback, $this->request, $this->response, $route->getParams());
        }

        /** EXECUTE FUNCTION **/
        if (is_callable($route->getCallback())) {
            return call_user_func($route->getCallback(), $this->request, $this->response, $route->getParams());
        }

        // Shouldn't ever reach this statement but just to be sure...
        throw new LogicException('Requires callback of type string|callable|array but callback with type ' . gettype($route->getCallback()) . ' passed instead!');
    }

    private function getRoute(string $method, string $path): ?Route
    {
        /** @var Route $route */
        foreach ($this->routes[$method] as $route) {
            if($route->match($path)) {
                return $route;
            }
        }
        return null;
    }
}