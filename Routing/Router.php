<?php

namespace Okami\Core\Routing;

use LogicException;
use Okami\Core\App;
use Okami\Core\Exceptions\NotFoundException;
use Okami\Core\Middlewares\Middleware;
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
    public array $serializedRoutes = [];

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
     *
     * @return Route
     */
    public function get(string $path, $callback): Route
    {
        return $this->map([Request::GET], $path, $callback);
    }

    /**
     * @param string $path
     * @param string|callable|array $callback
     *
     * @return Route
     */
    public function post(string $path, $callback): Route
    {
        return $this->map([Request::POST], $path, $callback);
    }

    /**
     * @param string $path
     * @param string|callable|array $callback
     *
     * @return Route
     */
    public function put(string $path, $callback): Route
    {
        return $this->map([Request::PUT], $path, $callback);
    }

    /**
     * @param string $path
     * @param string|callable|array $callback
     *
     * @return Route
     */
    public function delete(string $path, $callback): Route
    {
        return $this->map([Request::DELETE], $path, $callback);
    }

    /**
     * @param string $path
     * @param string|callable|array $callback
     *
     * @return Route
     */
    public function options(string $path, $callback): Route
    {
        return $this->map([Request::OPTIONS], $path, $callback);
    }

    /**
     * @param string $path
     * @param string|callable|array $callback
     *
     * @return Route
     */
    public function patch(string $path, $callback): Route
    {
        return $this->map([Request::PATCH], $path, $callback);
    }

    /**
     * @param string $path
     * @param string|callable|array $callback
     */
    public function any(string $path, $callback)
    {
        return $this->map([
            Request::GET,
            Request::POST,
            Request::PUT,
            Request::DELETE,
            Request::OPTIONS,
            Request::PATCH
        ], $path, $callback);
    }

    /**
     * @param array $methods
     * @param string $path
     * @param string|callable|array $callback
     *
     * @return Route
     */
    public function map(array $methods, string $path, $callback): Route
    {
        $route = null;

        /** RENDER TEMPLATE **/
        if (is_string($callback)) {
            $route = new TemplateRoute($path, $callback);
        }

        /** CALL CONTROLLER **/
        if (is_array($callback)) {
            $route = new ControllerRoute($path, $callback);
        }

        /** EXECUTE FUNCTION **/
        if (is_callable($callback)) {
            $route = new FunctionRoute($path, $callback);
        }

        if(is_null($route)) {
            throw new LogicException('Requires callback of type string|callable|array but callback with type ' . gettype($callback) . ' passed instead!');
        }

        $this->routes[] = $route;
        foreach ($methods as $method) {
            $this->serializedRoutes[$method][] = &$route;
        }

        return $route;
    }

    /**
     * @return Response
     * @throws NotFoundException
     */
    public function resolve(): Response
    {
        $path = $this->request->getPath();
        $method = $this->request->method();

        $route = $this->getRoute($method, $path);
        if (is_null($route)) {
            throw new NotFoundException();
        }

        if($route->hasMiddlewares()) {
            App::$app->addMiddlewares($route->middlewares);
        }

        if (App::$app->hasMiddlewares()) {
            App::$app->setCallstack($route);
            return App::$app->executeCallstack();
        } else {
            return $route->execute();
        }
    }

    private function getRoute(string $method, string $path): ?Route
    {
        /** @var Route $route */
        foreach ($this->serializedRoutes[$method] as $route) {
            if($route->match($path)) {
                return $route;
            }
        }
        return null;
    }
}