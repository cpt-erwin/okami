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
    /**
     * @var ResponseBody
     */
    public ResponseBody $body;

    /**
     * Response constructor.
     *
     */
    public function __construct()
    {
        $this->body = new ResponseBody();
    }

    /**
     * @param int $code
     */
    public function setStatusCode(int $code)
    {
        http_response_code($code);
    }

    /**
     * @param string $url
     */
    public function redirect(string $url)
    {
        header('Location: ' . $url);
    }
}