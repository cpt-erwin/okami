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
    /**
     * @param string $key
     * @param $value
     */
    public static function set(string $key, $value)
    {
        $_SESSION[$key] = $value;
    }

    /**
     * @param string $key
     *
     * @return mixed|null
     */
    public static function get(string $key)
    {
        return $_SESSION[$key] ?? null;
    }

    /**
     * @param string $key
     */
    public static function remove(string $key)
    {
        unset($_SESSION[$key]);
    }
}