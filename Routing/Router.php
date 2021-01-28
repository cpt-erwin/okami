<?php

namespace Okami\Core\Routing;

use LogicException;
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
     *
     * @return Route
     */
    public function get(string $path, $callback): Route
    {
        return $this->addRoute('get', $path, $callback);
    }

    /**
     * @param string $path
     * @param string|callable|array $callback
     *
     * @return Route
     */
    public function post(string $path, $callback): Route
    {
        return $this->addRoute('post', $path, $callback);
    }

    /**
     * @param string $method
     * @param string $path
     * @param string|callable|array $callback
     *
     * @return Route
     */
    private function addRoute(string $method, string $path, $callback): Route
    {
        /** RENDER TEMPLATE **/
        if (is_string($callback)) {
            return $this->routes[$method][] = new TemplateRoute($path, $callback);
        }

        /** CALL CONTROLLER **/
        if (is_array($callback)) {
            return $this->routes[$method][] = new ControllerRoute($path, $callback);
        }

        /** EXECUTE FUNCTION **/
        if (is_callable($callback)) {
            return $this->routes[$method][] = new FunctionRoute($path, $callback);
        }

        // Shouldn't ever reach this statement but just to be sure...
        throw new LogicException('Requires callback of type string|callable|array but callback with type ' . gettype($callback) . ' passed instead!');
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

        return $route->execute();
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