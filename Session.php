<?php

namespace Okami\Core;

/**
 * Class Session
 *
 * @author Michal Tuček <michaltk1@gmail.com>
 * @package Okami\Core
 */
class Session
{
    public function set(string $key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public function get(string $key)
    {
        return $_SESSION[$key] ?? false;
    }

    public function remove(string $key)
    {
        unset($_SESSION[$key]);
    }
}