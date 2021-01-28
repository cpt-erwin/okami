<?php

namespace Okami\Core\Routing;

/**
 * Class ControllerRoute
 *
 * @author Michal Tuček <michaltk1@gmail.com>
 * @package Okami\Core\Routing
 */
abstract class ControllerRoute extends Route
{
    abstract public function withMiddleware(string $middlewareClass): ControllerRoute;
    abstract public function withMiddlewares(array $middlewareClasses): ControllerRoute;
}