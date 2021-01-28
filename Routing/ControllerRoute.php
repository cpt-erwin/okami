<?php

namespace Okami\Core\Routing;

use Okami\Core\App;
use Okami\Core\Response;

/**
 * Class ControllerRoute
 *
 * @author Michal Tuček <michaltk1@gmail.com>
 * @package Okami\Core\Routing
 */
class ControllerRoute extends Route
{
    public function handleCallback(): Response
    {
        $callback = $this->getCallback();
        App::$app->setController(new $callback[0]()); // create instance of passed controller
        App::$app->controller->action = $callback[1];
        $callback[0] = App::$app->getController();
        $response = new Response();
        $response->body = call_user_func($callback, App::$app->request, App::$app->response, $this->getParams());
        return $response;
    }
}