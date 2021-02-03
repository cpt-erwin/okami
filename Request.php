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
    const GET = 'get';
    const POST = 'post';
    const PUT = 'put';
    const DELETE = 'delete';
    const OPTIONS = 'options';
    const PATCH = 'patch';

    /**
     * @return string
     */
    public function getPath(): string
    {
        /** @var string $path */
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        $position = strpos($path, '?');
        if ($position === false) {
            return $path;
        }

        return substr($path, 0, $position);
    }

    /**
     * @return bool
     */
    public function isGet(): bool
    {
        return $this->method() === 'get';
    }

    /**
     * @return string
     */
    public function method(): string
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    /**
     * @return bool
     */
    public function isPost(): bool
    {
        return $this->method() === 'post';
    }

    /**
     * @return array
     */
    public function getBody(): array
    {
        $body = [];
        if ($this->method() === 'get') {
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
        if ($this->method() === 'post') {
            foreach ($_POST as $key => $value) {
                $body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }

        return $body;
    }
}