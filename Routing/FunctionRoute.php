<?php

namespace Okami\Core\Routing;

use Okami\Core\App;
use Okami\Core\Response;

/**
 * Class FunctionRoute
 *
 * @author Michal Tuček <michaltk1@gmail.com>
 * @package Okami\Core\Routing
 */
class FunctionRoute extends Route
{
    /**
     * @return Response
     */
    public function execute(): Response
    {
        // FIXME: Don't create new response but use Apps response instead!
        $response = new Response();
        $response->body = call_user_func($this->getCallback(), App::$app->request, App::$app->response,
            $this->getParams());

        return $response;
    }
}