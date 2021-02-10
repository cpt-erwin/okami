<?php

namespace Okami\Core\Routing;

use Okami\Core\HTTPMethod;
use Okami\Core\Routing\Routes\Route;

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
        HTTPMethod::GET => [],
        HTTPMethod::POST => [],
        HTTPMethod::PUT => [],
        HTTPMethod::DELETE => [],
        HTTPMethod::OPTIONS => [],
        HTTPMethod::PATCH => []
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
     * @param string $method
     *
     * @return Route[]
     */
    public function getRoutesForMethod(string $method): array
    {
        return $this->getRoutes()[$method];
    }

    /**
     * @return Route[]
     */
    private function getRoutesFromGroups(): array
    {
        if (empty($this->routeGroups)) {
            return [];
        }

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
}