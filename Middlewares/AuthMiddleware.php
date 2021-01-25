<?php

namespace Okami\Core\Middlewares;

use Okami\Core\App;
use Okami\Core\Exceptions\ForbiddenException;

/**
 * Class AuthMiddleware
 *
 * @author Michal Tuček <michaltk1@gmail.com>
 * @package Okami\Core\Middlewares
 */
class AuthMiddleware extends Middleware
{
    public array $actions = [];

    /**
     * AuthMiddleware constructor.
     *
     * @param array $actions
     */
    public function __construct(array $actions = [])
    {
        $this->actions = $actions;
    }

    /**
     * @throws ForbiddenException
     */
    public function execute()
    {
        if(App::isGuest()) {
            if (empty($this->actions) || in_array(App::$app->controller->action, $this->actions)) {
                throw new ForbiddenException();
            }
        }
    }
}