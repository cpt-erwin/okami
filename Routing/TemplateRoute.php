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
    /**
     * @return Response
     */
    public function execute(): Response
    {
        App::$app->response->body = file_get_contents(App::$ROOT_DIR . '/views/' . $this->getCallback());

        return App::$app->response;
    }
}