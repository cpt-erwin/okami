<?php

namespace Okami\Core\Exceptions;

use Exception;

/**
 * Class ForbiddenException
 *
 * @author Michal Tuček <michaltk1@gmail.com>
 * @package Okami\Core\Exceptions
 */
class ForbiddenException extends Exception
{
    protected $message = 'You don\'t have permission to access this page!';

    protected $code = 403;
}