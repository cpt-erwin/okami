<?php

namespace Okami\Core\Middlewares;

/**
 * Class Middleware
 *
 * @author Michal Tuček <michaltk1@gmail.com>
 * @package Okami\Core\Middlewares
 */
abstract class Middleware
{
    abstract public function execute();
}