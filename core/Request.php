<?php

namespace Okami\Core;

/**
 * Class Request
 *
 * @author Michal Tuček <michaltk1@gmail.com>
 * @package Okami\Core
 */
class Request
{
    public function getPath()
    {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        $position = strpos($path, '?');
        if ($position === false) {
            return $path;
        }
        return substr($path, 0, $position);
    }

    public function getMethod()
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    public function getBody()
    {
        $body = [];
        if ($this->getMethod() === 'get') {
            foreach ($_GET as $key => $value) {
                $body[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
                /**
                 * 1) Gets the content of the super global var $_GET
                 * 2) Searches for a $key
                 * 3) Takes the the value of $_GET[$key] and removes the invalid (dangerous?) characters
                 * 4) Returns the sanitized value
                 */
            }
        }
        if ($this->getMethod() === 'post') {
            foreach ($_GET as $key => $value) {
                $body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }

        return $body;
    }
}