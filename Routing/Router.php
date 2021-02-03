<?php

namespace Okami\Core\Routing;

use Okami\Core\App;
use Okami\Core\Exceptions\NotFoundException;
use Okami\Core\Request;
use Okami\Core\Response;

/**
 * Class Router
 *
 * @author Michal Tuček <michaltk1@gmail.com>
 * @package Okami\Core\Routing
 */
class Router extends Routable
{
    /**
     * @var Request
     */
    public Request $request;

    /**
     * @var Response
     */
    public Response $response;

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
        parent::__construct();
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

        if ($route->hasMiddlewares()) {
            App::$app->addMiddlewares($route->getMiddlewares());
        }

        if (App::$app->hasMiddlewares()) {
            App::$app->setCallstack($route);

            return App::$app->executeCallstack();
        } else {
            return $route->execute();
        }
    }

    /**
     * @param string $method
     * @param string $path
     *
     * @return Route|null
     */
    private function getRoute(string $method, string $path): ?Route
    {
        foreach ($this->getRoutes($method) as $route) {
            if ($route->match($path)) {
                return $route;
            }
        }

        return null;
    }
}