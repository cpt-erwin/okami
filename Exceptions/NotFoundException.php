<?php

namespace Okami\Core\Exceptions;

use Exception;

/**
 * Class NotFoundException
 *
 * @author Michal Tuček <michaltk1@gmail.com>
 * @package Okami\Core\Exceptions
 */
class NotFoundException extends Exception
{
    protected $message = 'Page was not found!';

    protected $code = 404;
}