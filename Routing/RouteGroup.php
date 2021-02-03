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
     */
    public function __construct(string $path)
    {
        $this->pathRoot = $path;
        parent::__construct();
    }
}