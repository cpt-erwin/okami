<?php

namespace Okami\Core;

/**
 * Class Response
 *
 * @author Michal Tuček <michaltk1@gmail.com>
 * @package Okami\Core
 */
class Response
{
    public function setStatusCode(int $code)
    {
        http_response_code($code);
    }

    public function redirect(string $url)
    {
        header('Location: ' . $url);
    }
}