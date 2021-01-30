<?php

namespace Okami\Core\Routing;

use Okami\Core\App;
use Okami\Core\Response;

/**
 * Class TemplateRoute
 *
 * @author Michal Tuček <michaltk1@gmail.com>
 * @package Okami\Core\Routing
 */
class TemplateRoute extends Route
{
    public function execute(): Response
    {
        // FIXME: Don't create new response but use Apps response instead!
        $response = new Response();
        $response->body = App::$app->view->renderView($this->getCallback());
        return $response;
    }
}