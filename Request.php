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
     * @return string
     */
    public function method(): string
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
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

    /**
     * @param string $method
     *
     * @return bool
     */
    public function isMethod(string $method): bool
    {
        return $this->method() === $method;
    }

    /**
     * @return bool
     */
    public function isGet(): bool
    {
        return $this->isMethod(HTTPMethod::GET);
    }

    /**
     * @return bool
     */
    public function isPost(): bool
    {
        return $this->isMethod(HTTPMethod::POST);
    }

    /**
     * @return bool
     */
    public function isPut(): bool
    {
        return $this->isMethod(HTTPMethod::PUT);
    }

    /**
     * @return bool
     */
    public function isDelete(): bool
    {
        return $this->isMethod(HTTPMethod::DELETE);
    }

    /**
     * @return bool
     */
    public function isOptions(): bool
    {
        return $this->isMethod(HTTPMethod::OPTIONS);
    }

    /**
     * @return bool
     */
    public function isPatch(): bool
    {
        return $this->isMethod(HTTPMethod::PATCH);
    }
}