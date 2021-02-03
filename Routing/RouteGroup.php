<?php

namespace Okami\Core\Routing;

/**
 * Class RouteGroup
 *
 * @author Michal Tuček <michaltk1@gmail.com>
 * @package Okami\Core\Routing
 */
class RouteGroup extends Routable
{
    /**
     * RouteGroup constructor.
     *
     * @param string $path
     * @param callable $callable
     */
    public function __construct(string $path, callable $callable)
    {
        $this->pathRoot = $path;
    }
}