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
    /**
     * @throws ForbiddenException
     */
    public function before()
    {
        if(App::isGuest()) {
            throw new ForbiddenException();
        }
    }

    public function after()
    {
        // TODO: Implement after() method.
    }
}