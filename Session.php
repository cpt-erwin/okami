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
    public static function set(string $key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key)
    {
        return $_SESSION[$key] ?? false;
    }

    public static function remove(string $key)
    {
        unset($_SESSION[$key]);
    }
}