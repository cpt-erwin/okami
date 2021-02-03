<?php

namespace Okami\Core\Routing;

use Okami\Core\Request;

/**
 * Class RouteCollection
 *
 * @author Michal Tuček <michaltk1@gmail.com>
 * @package Okami\Core\Routing
 */
class RouteCollection
{
    /**
     * @var array<string, Route[]>
     */
    private array $routes = [
        Request::GET => [],
        Request::POST => [],
        Request::PUT => [],
        Request::DELETE => [],
        Request::OPTIONS => [],
        Request::PATCH => []
    ];

    /**
     * @var RouteGroup[]
     */
    private array $routeGroups = [];

    /**
     * @param Route $route
     * @param string $method
     */
    public function addRoute(Route $route, string $method)
    {
        $this->routes[$method] = $route;
    }

    /**
     * @param RouteGroup $routeGroup
     */
    public function addRouteGroup(RouteGroup $routeGroup)
    {
        $this->routeGroups[] = $routeGroup;
    }

    /**
     * @return Route[]
     */
    private function getRoutesFromGroups(): array
    {
        if (empty($this->routeGroups)) return [];

        $routes = [];
        foreach ($this->routeGroups as $routeGroup) {
            $routes = array_merge_recursive($routes, $routeGroup->routeCollection->getRoutes());
        }

        return $routes;
    }

    /**
     * @return array<string, Route[]>
     */
    public function getRoutes(): array
    {
        return array_merge_recursive($this->routes, $this->getRoutesFromGroups());
    }

    /**
     * @param string $method
     *
     * @return Route[]
     */
    public function getRoutesForMethod(string $method): array
    {
        return $this->getRoutes()[$method];
    }
}