<?php

namespace Okami\Core\Routing;

use LogicException;
use Okami\Core\Request;

/**
 * Class Routable
 *
 * @author Michal Tuček <michaltk1@gmail.com>
 * @package Okami\Core\Routing
 */
abstract class Routable
{
    /**
     * @var string|null
     */
    protected ?string $pathRoot = null;

    /**
     * @var RouteCollection
     */
    public RouteCollection $routeCollection;

    /**
     * Routable constructor.
     */
    public function __construct()
    {
        $this->routeCollection = new RouteCollection();
    }

    /**
     * @param string $path
     * @param callable $callable
     *
     * @return RouteGroup
     */
    public function group(string $path, callable $callable): RouteGroup
    {
        $routeGroup = new RouteGroup($this->getPath($path), $callable);
        $this->routeCollection->addRouteGroup($routeGroup);

        return $routeGroup;
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
     *
     * @return Route
     */
    public function any(string $path, $callback): Route
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

        $path = $this->getPath($path);

        if (is_string($callback)) {
            /** RENDER TEMPLATE **/
            $route = new TemplateRoute($path, $callback);
        } elseif (is_array($callback)) {
            /** CALL CONTROLLER **/
            $route = new ControllerRoute($path, $callback);
        } elseif (is_callable($callback)) {
            /** EXECUTE FUNCTION **/
            $route = new FunctionRoute($path, $callback);
        }

        if (is_null($route)) {
            throw new LogicException('Requires callback of type string|callable|array but callback with type ' . gettype($callback) . ' passed instead!');
        }

        foreach ($methods as $method) {
            $this->routeCollection->addRoute($route, $method);
        }

        return $route;
    }

    /**
     * @param string $method
     *
     * @return Route[]
     */
    public function getRoutes(string $method): array
    {
        return $this->routeCollection->getRoutesForMethod($method);
    }

    /**
     * @param string $path
     *
     * @return string
     */
    private function getPath(string $path): string
    {
        if (!is_null($this->pathRoot)) {
            return $this->pathRoot . $path;
        }

        return $path;
    }
}