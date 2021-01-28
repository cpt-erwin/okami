<?php

namespace Okami\Core\Middlewares;

use Okami\Core\Response;
use Okami\Core\Routing\Route;

/**
 * Class Middleware
 *
 * @author Michal Tuček <michaltk1@gmail.com>
 * @package Okami\Core\Middlewares
 */
abstract class Middleware
{
    private Route $route;

    private Response $response;

    public function __construct(Route $route)
    {
        $this->route = $route;
        $this->response = new Response();
    }

    public function execute(): Response
    {
        $this->before();

        if($this->route->hasPendingMiddlewares()) {
            $this->response = $this->route->callNextMiddleware();
        } else {
            $this->response = $this->route->handleCallback();
        }

        $this->after();

        return $this->response;
    }

    abstract public function before();
    abstract public function after();
}