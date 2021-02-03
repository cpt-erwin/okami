<?php

namespace Okami\Core\Routing;

use Okami\Core\Traits\WithMiddlewaresTrait;

/**
 * Class RouteGroup
 *
 * @author Michal Tuček <michaltk1@gmail.com>
 * @package Okami\Core\Routing
 */
class RouteGroup extends Routable
{
    use WithMiddlewaresTrait {
        WithMiddlewaresTrait::addMiddleware as private;
        WithMiddlewaresTrait::addMiddlewares as private;
    }

    /**
     * RouteGroup constructor.
     *
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->pathRoot = $path;
        parent::__construct();
    }

    /**
     * @param string $middleware
     *
     * @return $this
     */
    public function withMiddleware(string $middleware): RouteGroup
    {
        $this->withMiddlewares([$middleware]);

        return $this;
    }

    /**
     * @param array $middlewares
     *
     * @return $this
     */
    public function withMiddlewares(array $middlewares): RouteGroup
    {
        foreach ($this->routeCollection->getRoutes() as $method => $routes) {
            foreach ($routes as $route) {
                $route->addMiddlewares($middlewares);
            }
        }

        return $this;
    }
}